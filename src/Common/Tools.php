<?php

namespace NFePHP\NFSeProdam\Common;

/**
 * Auxiar Tools Class for comunications with NFSe webserver in Nacional Standard
 *
 * @category  NFePHP
 * @package   NFePHP\NFSeProdam
 * @copyright NFePHP Copyright (c) 2020
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse-prodam for the canonical source repository
 */
use NFePHP\Common\Certificate;
use NFePHP\Common\Strings;
use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSeProdam\RpsInterface;
use NFePHP\NFSeProdam\Common\Signer;
use NFePHP\NFSeProdam\Common\Soap\SoapInterface;
use NFePHP\NFSeProdam\Common\Soap\SoapCurl;

class Tools
{

    public $lastRequest;
    protected $config;
    protected $remetente;
    protected $certificate;
    protected $wsobj;
    protected $soap;
    protected $environment;

    /**
     * Constructor
     * @param string $config
     * @param Certificate $cert
     */
    public function __construct($config, Certificate $cert)
    {
        $this->config = json_decode($config);
        $this->certificate = $cert;
        $this->wsobj = $this->loadWsobj($this->config->cmun);
        $this->environment = 'homologacao';
        if ($this->config->tpamb === 1) {
            $this->environment = 'producao';
        }
        $this->buildRemetenteTag();
    }

    /**
     * Build tag Rementente
     */
    protected function buildRemetenteTag()
    {
        $this->remetente = "<CPFCNPJRemetente>";
        if (!empty($this->config->cnpj)) {
            $this->remetente .= "<CNPJ>{$this->config->cnpj}</CNPJ>";
        } else {
            $this->remetente .= "<CPF>{$this->config->cpf}</CPF>";
        }
        $this->remetente .= "</CPFCNPJRemetente>";
    }

    /**
     * load webservice parameters
     * @param string $cmun
     * @return object
     * @throws \Exception
     */
    protected function loadWsobj($cmun)
    {
        $path = realpath(__DIR__ . "/../../storage/urls_webservices.json");
        $urls = json_decode(file_get_contents($path), true);
        if (empty($urls[$cmun])) {
            throw new \Exception("Não localizado parâmetros para esse municipio.");
        }
        return (object) $urls[$cmun];
    }

    /**
     * SOAP communication dependency injection
     * @param SoapInterface $soap
     */
    public function loadSoapClass(SoapInterface $soap)
    {
        $this->soap = $soap;
    }

    /**
     * Sign XML passing in content
     * @param string $content
     * @param string $tagname
     * @param string $mark
     * @return string XML signed
     */
    public function sign($content, $tagname, $mark)
    {
        $xml = Signer::sign(
            $this->certificate,
            $content,
            $tagname,
            $mark
        );
        return $xml;
    }

    /**
     * Send message to webservice
     * @param string $message
     * @param string $operation
     * @param string $mode sincrono ou assincrono
     * @return string XML response from webservice
     */
    public function send($message, $operation, $mode)
    {
        $action = "{$this->wsobj->soapns}/ws/$operation";
        if ($operation == 'TesteEnvioLoteRPS') {
            $action = "{$this->wsobj->soapns}/ws/testeenvio";
        }
        $modo = "{$mode}_homologacao";
        if ($this->environment === 'producao') {
            $modo = "{$mode}_homologacao";
        }
        $url = $this->wsobj->$modo;
        if (empty($url)) {
            throw new \Exception("Não está registrada a URL para o ambiente "
            . "de {$this->environment} desse municipio.");
        }
        $request = $this->createSoapRequest($message, $operation);
        $this->lastRequest = $request;

        if (empty($this->soap)) {
            $this->soap = new SoapCurl($this->certificate);
        }
        $msgSize = strlen($request);

        $parameters = [
            "Accept-Encoding: gzip,deflate",
            "Content-Type: application/soap+xml;charset=UTF-8;action=\"{$action}\"",
            "Content-length: $msgSize"
        ];
        if ($mode == 'assincrono') {
            $parameters = [
                "Accept-Encoding: gzip,deflate",
                "Content-Type: application/soap+xml;charset=UTF-8",
                "SOAPAction: \"{$action}\"",
                "Content-length: $msgSize"
            ];
        }
        $response = (string) $this->soap->send(
            $operation,
            $url,
            $action,
            $request,
            $parameters
        );
        return $this->extractContentFromResponse($response, $operation);
    }

    /**
     * Extract xml response from CDATA outputXML tag
     * @param string $response Return from webservice
     * @return string XML extracted from response
     */
    protected function extractContentFromResponse($response, $operation)
    {
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($response);
        if (!empty($dom->getElementsByTagName('RetornoXML')->item(0))) {
            $node = $dom->getElementsByTagName('RetornoXML')->item(0);
            $text = str_replace("ISO-8859-1", "UTF-8", $node->textContent);
            return Strings::normalize($text);
        }
        return $response;
    }

    /**
     * Build SOAP request
     * @param string $message
     * @param string $operation
     * @return string XML SOAP request
     */
    protected function createSoapRequest($message, $operation)
    {
        //$cdata = htmlspecialchars($message, ENT_NOQUOTES);
        $env = "<soap:Envelope xmlns:soap=\"http://www.w3.org/2003/05/soap-envelope\" "
            . "xmlns:nfe=\"{$this->wsobj->soapns}\">"
            . "<soap:Header/>"
            . "<soap:Body>"
            . "<nfe:{$operation}Request>"
            . "<nfe:VersaoSchema>{$this->wsobj->version}</nfe:VersaoSchema>"
            . "<nfe:MensagemXML></nfe:MensagemXML>"
            . "</nfe:{$operation}Request>"
            . "</soap:Body>"
            . "</soap:Envelope>";

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = false;
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($env);
        $node = $dom->getElementsByTagName('MensagemXML')->item(0);
        $node->appendChild($dom->createCDATASection($message));
        $env = $dom->saveXML();

        //header("Content-type: text/xml");
        //echo $env;
        //die;
        return $env;
    }
}
