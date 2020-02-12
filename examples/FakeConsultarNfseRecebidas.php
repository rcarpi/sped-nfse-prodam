<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '../bootstrap.php';

use NFePHP\Common\Certificate;
use NFePHP\NFSeProdam\Tools;
use NFePHP\NFSeProdam\Common\Soap\SoapFake;
use NFePHP\NFSeProdam\Common\FakePretty;

try {
    
    $config = [
        'cnpj' => '99999999000191',
        'im' => '12345678',
        'cmun' => '3550308', //ira determinar as urls e outros dados
        'razao' => 'Empresa Test Ltda',
        'tpamb' => 2
    ];

    $configJson = json_encode($config);

    $content = file_get_contents('expired_certificate.pfx');
    $password = 'associacao';
    $cert = Certificate::readPfx($content, $password);
    
    $soap = new SoapFake();
    $soap->disableCertValidation(true);
    
    $tools = new Tools($configJson, $cert);
    $tools->loadSoapClass($soap);

    $dtInicial = '2019-12-01';
    $dtFinal = '2019-12-31';
    $pagina = 1;
        
    $response = $tools->consultarNfseRecebidas($dtInicial, $dtFinal, $pagina);
    
    echo FakePretty::prettyPrint($response, '');
 
} catch (\Exception $e) {
    echo $e->getMessage();
}
