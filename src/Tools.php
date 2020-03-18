<?php

namespace NFePHP\NFSeProdam;

/**
 * Class for comunications with NFSe webserver in Nacional Standard
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

use NFePHP\NFSeProdam\Common\Tools as BaseTools;
use NFePHP\NFSeProdam\RpsInterface;
use NFePHP\NFSeProdam\Common\Signer;
use NFePHP\Common\Certificate;
use NFePHP\Common\Validator;

class Tools extends BaseTools
{
    protected $xsdpath;
    protected $nsxsi = 'http://www.w3.org/2001/XMLSchema-instance';
    protected $nsxsd = 'http://www.w3.org/2001/XMLSchema';
    protected $algorithm = OPENSSL_ALGO_SHA1;

    /**
     * Constructor
     * @param string $config
     * @param Certificate $cert
     */
    public function __construct($config, Certificate $cert)
    {
        parent::__construct($config, $cert);
        $path = realpath(
            __DIR__ . '/../storage/schemes'
        );
        $this->xsdpath = $path;
    }
    
    /**
     * Consulta de CNPJ de Contribuinte do Municipio de São Paulo (SINCRONO)
     * @param string $cnpj
     * @param string $cpf
     * @return string
     */
    public function consultarCnpj($cnpj = null, $cpf = null)
    {
        $operation = "ConsultaCNPJ";
        $mode = 'sincrono';
        $content = "<PedidoConsultaCNPJ "
            . "xmlns:xsd=\"{$this->nsxsd}\" "
            . "xmlns:xsi=\"{$this->nsxsi}\" "
            . "xmlns=\"{$this->wsobj->msgns}\">"
            . "<Cabecalho xmlns=\"\" Versao=\"{$this->wsobj->version}\">"
            . $this->remetente
            . "</Cabecalho>"
            . "<CNPJContribuinte xmlns=\"\">"
            . "<CNPJ>{$cnpj}</CNPJ>"
            . "</CNPJContribuinte>"
            . "</PedidoConsultaCNPJ>";
       
        
        $content = Signer::sign(
            $this->certificate,
            $content,
            'PedidoConsultaCNPJ',
            '',
            $this->algorithm,
            [false, false, null, null],
            'PedidoConsultaCNPJ'
        );
        
        $content = str_replace(
            ['<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8"?>'],
            '',
            $content
        );
        
        Validator::isValid($content, $this->xsdpath . '/PedidoConsultaCNPJ_v01.xsd');
        return $this->send($content, $operation, $mode);
    }
    
    /**
     * Consulta NFSe emitidas em determinado periodo (SINCRONO)
     * @param date $dtInicial
     * @param date $dtFinal
     * @param integer $pagina
     * @return string
     */
    public function consultarNfsePeriodo(
        $dtInicial,
        $dtFinal,
        $pagina = 1
    ) {
        $operation = "ConsultaNFeEmitidas";
        $mode = 'sincrono';
        $content = $this->consulta($dtInicial, $dtFinal, $pagina);
        return $this->send($content, $operation, $mode);
    }

    /**
     * Consulta NFSe recebidas determinado periodo (SINCRONO)
     * @param type $dtInicial
     * @param type $dtFinal
     * @param type $pagina
     * @return type]
     */
    public function consultarNfseRecebidas(
        $dtInicial,
        $dtFinal,
        $pagina = 1
    ) {
        $operation = "ConsultaNFeRecebidas";
        $mode = "sincrono";
        $content = $this->consulta($dtInicial, $dtFinal, $pagina);
        return $this->send($content, $operation, $mode);
    }

    /**
     * Consulta NFSe emitidas ou RPS convertidos em NFSe (SINCRONO)
     * @param array $nfses
     * @param array $rpss
     * @return string
     * @throws Exception
     */
    public function consultarNfse(array $nfses = [], array $rpss = [])
    {
        if (count($nfses) + count($rpss) > 50) {
            throw new Exception("O limite é de 50 documentos por consulta");
        }

        $operation = "ConsultaNFe";
        $mode = 'sincrono';
        
        $consulta = "";
        if (!empty($rpss)) {
            foreach ($rpss as $rps) {
                $orps = (object) $rps;
                $consulta .= "<Detalhe xmlns=\"\">"
                    . "<ChaveRPS>"
                    . "<InscricaoPrestador>{$this->config->im}</InscricaoPrestador>"
                    . "<SerieRPS>{$orps->serie}</SerieRPS>"
                    . "<NumeroRPS>{$orps->numero}</NumeroRPS>"
                    . "</ChaveRPS>"
                    . "</Detalhe>";
            }
        }
        if (!empty($nfses)) {
            foreach ($nfses as $nfse) {
                $consulta .= "<Detalhe xmlns=\"\">"
                    . "<ChaveNFe>"
                    . "<InscricaoPrestador>{$this->config->im}</InscricaoPrestador>"
                    . "<NumeroNFe>{$nfse}</NumeroNFe>"
                    . "</ChaveNFe>"
                    . "</Detalhe>";
            }
        }
        
        $content = "<PedidoConsultaNFe "
            . "xmlns:xsd=\"{$this->nsxsd}\" "
            . "xmlns:xsi=\"{$this->nsxsi}\" "
            . "xmlns=\"{$this->wsobj->msgns}\">"
            . "<Cabecalho xmlns=\"\" Versao=\"{$this->wsobj->version}\">"
            . $this->remetente
            . "</Cabecalho>"
            . $consulta
            . "</PedidoConsultaNFe>";

        $content = Signer::sign(
            $this->certificate,
            $content,
            'PedidoConsultaNFe',
            '',
            $this->algorithm,
            [false, false, null, null],
            'PedidoConsultaNFe'
        );
        $content = str_replace(
            ['<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8"?>'],
            '',
            $content
        );
        Validator::isValid($content, $this->xsdpath . '/PedidoConsultaNFe_v01.xsd');
        return $this->send($content, $operation, $mode);
    }

    /**
     * Consulta de Lote de RPS  (SINCRONO)
     * @param int $lote
     * @return string
     */
    public function consultarLoteRps($lote)
    {
        $operation = "ConsultaLote";
        $mode = "sincrono";
        
        $content = "<PedidoConsultaLote "
            . "xmlns:xsd=\"{$this->nsxsd}\" "
            . "xmlns:xsi=\"{$this->nsxsi}\" "
            . "xmlns=\"{$this->wsobj->msgns}\">"
            . "<Cabecalho xmlns=\"\" Versao=\"{$this->wsobj->version}\">"
            . $this->remetente
            . "<NumeroLote>{$lote}</NumeroLote>"
            . "</Cabecalho>"
            . "</PedidoConsultaLote>";
            
        $content = Signer::sign(
            $this->certificate,
            $content,
            'PedidoConsultaLote',
            '',
            $this->algorithm,
            [false, false, null, null],
            'PedidoConsultaLote'
        );
        $content = str_replace(
            ['<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8"?>'],
            '',
            $content
        );
        Validator::isValid($content, $this->xsdpath . '/PedidoConsultaLote_v01.xsd');
        return $this->send($content, $operation, $mode);
    }
    
    /**
     * Consulta de Informações de Lote de RPS  (SINCRONO)
     * @param int $lote
     * @return string
     */
    public function consultarInformacaoLoteRps($lote = null)
    {
        $operation = "ConsultaInformacoesLote";
        $mode = "sincrono";
        
        $content = "<PedidoInformacoesLote "
            . "xmlns:xsd=\"{$this->nsxsd}\" "
            . "xmlns:xsi=\"{$this->nsxsi}\" "
            . "xmlns=\"{$this->wsobj->msgns}\">"
            . "<Cabecalho xmlns=\"\" Versao=\"{$this->wsobj->version}\">"
            . $this->remetente;
        $content .= !empty($lote) ? "<NumeroLote>{$lote}</NumeroLote>" : '';
        $content .= "<InscricaoPrestador>{$this->config->im}</InscricaoPrestador>"
            . "</Cabecalho>"
            . "</PedidoInformacoesLote>";
            
        $content = Signer::sign(
            $this->certificate,
            $content,
            'PedidoInformacoesLote',
            '',
            $this->algorithm,
            [false, false, null, null],
            'PedidoInformacoesLote'
        );
        $content = str_replace(
            ['<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8"?>'],
            '',
            $content
        );
        Validator::isValid($content, $this->xsdpath . '/PedidoInformacoesLote_v01.xsd');
        return $this->send($content, $operation, $mode);
    }
    
    /**
     * Consulta Situação do Lote de RPS (ASSINCRONO)
     * @param string $protocolo
     * @return string
     */
    public function consultarSituacaoLoteRps($protocolo)
    {
        $operation = "ConsultaSituacaoLote";
        $mode = "assincrono";
        
        $content = "<PedidoConsultaSituacaoLote "
            . "xmlns:xsd=\"{$this->nsxsd}\" "
            . "xmlns:xsi=\"{$this->nsxsi}\" "
            . "xmlns=\"{$this->wsobj->msgns}\">"
            . "<CPFCNPJRemetente xmlns=\"\">"
            . "<CNPJ>{$this->config->cnpj}</CNPJ>"
            . "</CPFCNPJRemetente>"
            . "<NumeroProtocolo xmlns=\"\">{$protocolo}</NumeroProtocolo>"
            . "</PedidoConsultaSituacaoLote>";
            
        //Validator::isValid($content, $this->xsdpath . '/ConsultaSituacaoLoteAsync_v01.xsd');
        return $this->send($content, $operation, $mode);
    }
    
    /**
     * Envia Teste de Lote RPS (SINCRONO)
     * @param array $rps
     * @return string
     */
    public function envioTesteRpsSincrono(array $rpss)
    {
        $txtRps = "";
        $dt = [];
        $valorTotServicos = 0;
        $valorTotDeducoes = 0;
        $qtdRps = count($rpss);
        foreach ($rpss as $rps) {
            $rps->config($this->config);
            $rps->addCertificate($this->certificate);
            $dt[] = $rps->std->dataemissao;
            $valorTotServicos += $rps->std->valorservicos;
            $valorTotDeducoes += $rps->std->valordeducoes;
            $txtRps .= str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $rps->render());
        }
        $dtInicio = $this->minDate($dt);
        $dtFim = $this->maxDate($dt);
        
        $operation= "TesteEnvioLoteRPS";
        
        $content = "<PedidoEnvioLoteRPS "
            . "xmlns:xsd=\"{$this->nsxsd}\" "
            . "xmlns:xsi=\"{$this->nsxsi}\" "
            . "xmlns=\"{$this->wsobj->msgns}\">"
            . "<Cabecalho xmlns=\"\" Versao=\"{$this->wsobj->version}\">"
            . $this->remetente
            . "<transacao>false</transacao>"
            . "<dtInicio>{$dtInicio}</dtInicio>"
            . "<dtFim>{$dtFim}</dtFim>"
            . "<QtdRPS>{$qtdRps}</QtdRPS>"
            . "<ValorTotalServicos>{$valorTotServicos}</ValorTotalServicos>"
            . "<ValorTotalDeducoes>{$valorTotDeducoes}</ValorTotalDeducoes>"
            . "</Cabecalho>"
            . $txtRps
            . "</PedidoEnvioLoteRPS>";
            
        $content = Signer::sign(
            $this->certificate,
            $content,
            'PedidoEnvioLoteRPS',
            '',
            $this->algorithm,
            [false, false, null, null],
            'PedidoEnvioLoteRPS'
        );
        $content = str_replace(
            ['<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8"?>'],
            '',
            $content
        );
        Validator::isValid($content, $this->xsdpath . '/PedidoEnvioLoteRPS_v01.xsd');
        return $this->send($content, $operation, 'sincrono');
    }
    
    /**
     * Envia lote RPS (Assincrono)
     * @param array $rpss
     * @return string
     */
    public function envioTesteRpsAssincrono(array $rpss)
    {
        $txtRps = "";
        $dt = [];
        $valorTotServicos = 0;
        $valorTotDeducoes = 0;
        $qtdRps = count($rpss);
        foreach ($rpss as $rps) {
            $rps->config($this->config);
            $rps->addCertificate($this->certificate);
            $dt[] = $rps->std->dataemissao;
            $valorTotServicos += $rps->std->valorservicos;
            $valorTotDeducoes += $rps->std->valordeducoes;
            $txtRps .= str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $rps->render());
        }

        $dtInicio = $this->minDate($dt);
        $dtFim = $this->maxDate($dt);
        
        $operation= "TesteEnvioLoteRpsAsync";
        
        $content = "<PedidoEnvioLoteRPS "
            . "xmlns:xsd=\"{$this->nsxsd}\" "
            . "xmlns:xsi=\"{$this->nsxsi}\" "
            . "xmlns=\"{$this->wsobj->msgns}\">"
            . "<Cabecalho xmlns=\"\" Versao=\"{$this->wsobj->version}\">"
            . $this->remetente
            . "<transacao>false</transacao>"
            . "<dtInicio>{$dtInicio}</dtInicio>"
            . "<dtFim>{$dtFim}</dtFim>"
            . "<QtdRPS>{$qtdRps}</QtdRPS>"
            . "<ValorTotalServicos>{$valorTotServicos}</ValorTotalServicos>"
            . "<ValorTotalDeducoes>{$valorTotDeducoes}</ValorTotalDeducoes>"
            . "</Cabecalho>"
            . $txtRps
            . "</PedidoEnvioLoteRPS>";
            
        $content = Signer::sign(
            $this->certificate,
            $content,
            'PedidoEnvioLoteRPS',
            '',
            $this->algorithm,
            [false, false, null, null],
            'PedidoEnvioLoteRPS'
        );
        $content = str_replace(
            ['<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8"?>'],
            '',
            $content
        );
        Validator::isValid($content, $this->xsdpath . '/PedidoEnvioLoteRPS_v01.xsd');
        return $this->send($content, $operation, 'assincrono');
    }
    
    /**
     * Enviar RSP (SINCRONO)
     * @param Rps $rpss
     * @return string
     */
    public function envioRpsSincrono(Rps $rps)
    {
        $rps->config($this->config);
        $rps->addCertificate($this->certificate);
        $txtRps = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $rps->render());
               
        $operation= "EnvioRPS";
        
        $content = "<PedidoEnvioRPS "
            . "xmlns:xsd=\"{$this->nsxsd}\" "
            . "xmlns:xsi=\"{$this->nsxsi}\" "
            . "xmlns=\"{$this->wsobj->msgns}\">"
            . "<Cabecalho xmlns=\"\" Versao=\"{$this->wsobj->version}\">"
            . $this->remetente
            . "</Cabecalho>"
            . $txtRps
            . "</PedidoEnvioRPS>";
            
        $content = Signer::sign(
            $this->certificate,
            $content,
            'PedidoEnvioRPS',
            '',
            $this->algorithm,
            [false, false, null, null],
            'PedidoEnvioRPS'
        );
        $content = str_replace(
            ['<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8"?>'],
            '',
            $content
        );
        Validator::isValid($content, $this->xsdpath . '/PedidoEnvioRPS_v01.xsd');
        return $this->send($content, $operation, 'sincrono');
    }
    
    /**
     * Enviar Lote de RSP (SINCRONO)
     * @param array $rpss
     * @return string
     */
    public function envioLoteRpsSincrono(array $rpss = [])
    {
        $txtRps = "";
        $dt = [];
        $valorTotServicos = 0;
        $valorTotDeducoes = 0;
        $qtdRps = count($rpss);
        foreach ($rpss as $rps) {
            $rps->config($this->config);
            $rps->addCertificate($this->certificate);
            $dt[] = $rps->std->dataemissao;
            $valorTotServicos += $rps->std->valorservicos;
            $valorTotDeducoes += $rps->std->valordeducoes;
            $txtRps .= str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $rps->render());
        }
        $dtInicio = $this->minDate($dt);
        $dtFim = $this->maxDate($dt);
               
        $operation= "EnvioLoteRPS";
        
        $content = "<PedidoEnvioLoteRPS "
            . "xmlns:xsd=\"{$this->nsxsd}\" "
            . "xmlns:xsi=\"{$this->nsxsi}\" "
            . "xmlns=\"{$this->wsobj->msgns}\">"
            . "<Cabecalho xmlns=\"\" Versao=\"{$this->wsobj->version}\">"
            . $this->remetente
            . "<transacao>false</transacao>"
            . "<dtInicio>{$dtInicio}</dtInicio>"
            . "<dtFim>{$dtFim}</dtFim>"
            . "<QtdRPS>{$qtdRps}</QtdRPS>"
            . "<ValorTotalServicos>{$valorTotServicos}</ValorTotalServicos>"
            . "<ValorTotalDeducoes>{$valorTotDeducoes}</ValorTotalDeducoes>"
            . "</Cabecalho>"
            . $txtRps
            . "</PedidoEnvioLoteRPS>";
            
        $content = Signer::sign(
            $this->certificate,
            $content,
            'PedidoEnvioLoteRPS',
            '',
            $this->algorithm,
            [false, false, null, null],
            'PedidoEnvioLoteRPS'
        );
        $content = str_replace(
            ['<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8"?>'],
            '',
            $content
        );
        Validator::isValid($content, $this->xsdpath . '/PedidoEnvioLoteRPS_v01.xsd');
        return $this->send($content, $operation, 'sincrono');
    }
    
    /**
     * Enviar RPS ou Lote de RSP (ASSINCRONO)
     * @param array $rpss
     * @return string
     */
    public function envioLoteRpsAssincrono(array $rpss = [])
    {
        if ($this->config->tpamb == 2) {
            throw new \Exception('Não existe ambiente de homologação para envio assincrono!');
        }
        $txtRps = "";
        $dt = [];
        $valorTotServicos = 0;
        $valorTotDeducoes = 0;
        $qtdRps = count($rpss);
        foreach ($rpss as $rps) {
            $rps->config($this->config);
            $rps->addCertificate($this->certificate);
            $dt[] = $rps->std->dataemissao;
            $valorTotServicos += $rps->std->valorservicos;
            $valorTotDeducoes += $rps->std->valordeducoes;
            $txtRps .= str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $rps->render());
        }
        $dtInicio = $this->minDate($dt);
        $dtFim = $this->maxDate($dt);
               
        $operation= "EnvioLoteRpsAsync";
        
        
        $content = "<PedidoEnvioLoteRPS "
            . "xmlns:xsd=\"{$this->nsxsd}\" "
            . "xmlns:xsi=\"{$this->nsxsi}\" "
            . "xmlns=\"{$this->wsobj->msgns}\">"
            . "<Cabecalho xmlns=\"\" Versao=\"{$this->wsobj->version}\">"
            . $this->remetente
            . "<transacao>false</transacao>"
            . "<dtInicio>{$dtInicio}</dtInicio>"
            . "<dtFim>{$dtFim}</dtFim>"
            . "<QtdRPS>{$qtdRps}</QtdRPS>"
            . "<ValorTotalServicos>{$valorTotServicos}</ValorTotalServicos>"
            . "<ValorTotalDeducoes>{$valorTotDeducoes}</ValorTotalDeducoes>"
            . "</Cabecalho>"
            . $txtRps
            . "</PedidoEnvioLoteRPS>";
            
        $content = Signer::sign(
            $this->certificate,
            $content,
            'PedidoEnvioLoteRPS',
            '',
            $this->algorithm,
            [false, false, null, null],
            'PedidoEnvioLoteRPS'
        );
        $content = str_replace(
            ['<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8"?>'],
            '',
            $content
        );
        Validator::isValid($content, $this->xsdpath . '/PedidoEnvioLoteRPS_v01.xsd');
        return $this->send($content, $operation, 'assincrono');
    }
    
    /**
     * Cancelar NFSe
     * @param int $numero
     * @return string
     */
    public function cancelarNfse($numero)
    {
        $sign = $this->signStrCancel($numero);
        
        $operation = "CancelamentoNFe";
        $mode = 'sincrono';
        
        $content = "<PedidoCancelamentoNFe "
            . "xmlns:xsd=\"{$this->nsxsd}\" "
            . "xmlns:xsi=\"{$this->nsxsi}\" "
            . "xmlns=\"{$this->wsobj->msgns}\">"
            . "<Cabecalho xmlns=\"\" Versao=\"{$this->wsobj->version}\">"
            . $this->remetente
            . "<transacao>true</transacao>"
            . "</Cabecalho>"
            . "<Detalhe xmlns=\"\">"
            . "<ChaveNFe>"
            . "<InscricaoPrestador>{$this->config->im}</InscricaoPrestador>"
            . "<NumeroNFe>{$numero}</NumeroNFe>"
            . "</ChaveNFe>"
            . "<AssinaturaCancelamento>{$sign}</AssinaturaCancelamento>"
            . "</Detalhe>"
            . "</PedidoCancelamentoNFe>";
            
        $content = Signer::sign(
            $this->certificate,
            $content,
            'PedidoCancelamentoNFe',
            '',
            $this->algorithm,
            [false, false, null, null],
            'PedidoCancelamentoNFe'
        );
        $content = str_replace(
            ['<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8"?>'],
            '',
            $content
        );
        Validator::isValid($content, $this->xsdpath . '/PedidoCancelamentoNFe_v01.xsd');
        return $this->send($content, $operation, $mode);
    }
    
    /**
     * Assinatura adcional da string de cancelamento
     * @param integer $numero
     * @return string
     */
    private function signStrCancel($numero)
    {
        $im = str_pad($this->config->im, 8, '0', STR_PAD_LEFT);
        $num = str_pad($numero, 12, '0', STR_PAD_LEFT);
        $signature = base64_encode($this->certificate->sign($im . $num, OPENSSL_ALGO_SHA1));
        return $signature;
    }

    /**
     * Monta a consulta por periodo para emitidas ou recebidas
     * @param string $dtInicial
     * @param string $dtFinal
     * @param int $pagina
     * @return string
     */
    protected function consulta(
        $dtInicial,
        $dtFinal,
        $pagina = 1
    ) {
        $content = "<PedidoConsultaNFePeriodo "
            . "xmlns:xsd=\"{$this->nsxsd}\" "
            . "xmlns:xsi=\"{$this->nsxsi}\" "
            . "xmlns=\"{$this->wsobj->msgns}\">"
            . "<Cabecalho xmlns=\"\" Versao=\"{$this->wsobj->version}\">"
            . $this->remetente
            . "<CPFCNPJ>";
        if (!empty($this->config->cnpj)) {
            $content .= "<CNPJ>{$this->config->cnpj}</CNPJ>";
        } else {
            $content .= "<CPF>{$this->config->cpf}</CPF>";
        }
        $content .= "</CPFCNPJ>"
            . "<Inscricao>{$this->config->im}</Inscricao>"
            . "<dtInicio>{$dtInicial}</dtInicio>"
            . "<dtFim>{$dtFinal}</dtFim>"
            . "<NumeroPagina>{$pagina}</NumeroPagina>"
            . "</Cabecalho>"
            . "</PedidoConsultaNFePeriodo>";
        
        $content = Signer::sign(
            $this->certificate,
            $content,
            'PedidoConsultaNFePeriodo',
            '',
            $this->algorithm,
            [false, false, null, null],
            'PedidoConsultaNFePeriodo'
        );
        $content = str_replace(
            ['<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8"?>'],
            '',
            $content
        );
        Validator::isValid($content, $this->xsdpath . '/PedidoConsultaNFePeriodo_v01.xsd');
        return $content;
    }

    /**
     * Localiza a maior data em um Array com as datas de emissão
     * @param array $dt
     * @return string
     */
    protected function maxDate(array $dt)
    {
        return max($dt);
    }
    
    /**
     * Localiza a menor data em um Array com as datas de emissão
     * @param array $dt
     * @return string
     */
    protected function minDate(array $dt)
    {
        return min($dt);
    }
}
