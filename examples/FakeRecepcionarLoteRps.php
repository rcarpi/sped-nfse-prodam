<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '../bootstrap.php';

use NFePHP\Common\Certificate;
use NFePHP\NFSeProdam\Tools;
use NFePHP\NFSeProdam\Rps;
use NFePHP\NFSeProdam\Common\Soap\SoapFake;
use NFePHP\NFSeProdam\Common\FakePretty;

try {

    $config = [
        'cnpj'  => '99999999000191',
        'im'    => '12345678',
        'cmun'  => '3550308', //ira determinar as urls e outros dados
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

    $arps = [];

    $std = new \stdClass();
    $std = new \stdClass();
    $std->version = '1'; //opcional
    $std->numero = 11; //obrigatorio
    $std->serie = 'U1'; //obrigatorio
    $std->tipo = 'RPS'; //obrigatorio //RPS RPS-C RPS-M 
    $std->dataemissao = '2018-10-31'; //obrigatorio
    $std->status = 'N'; //N - normal C- cancelado obrigatorio
    $std->tributacao = 'T'; //T – Tributado em São Paulo
    //F – Tributado Fora de São Paulo
    //A – Tributado em São Paulo, porém Isento
    //B – Tributado Fora de São Paulo, porém Isento
    //D – Tributado em São Paulo com isenção parcial
    //M - Tributado em São Paulo, porém com indicação de imunidade subjetiva
    //N - Tributado fora de São Paulo, porém com indicação de imunidade subjetiva
    //R - Tributado em São Paulo, porém com indicação de imunidade objetiva
    //S - Tributado fora de São Paulo, porém com indicação de imunidade objetiva
    //X –Tributado em São Paulo, porém Exigibilidade Suspensa
    //V –Tributado Fora de São Paulo, porém Exigibilidade Suspensa
    //P – Exportação de Serviços
    //C – Cancelado

    $std->codigoservico = '2658';
    $std->discriminacao = 'Detalhes do serviço';
    $std->valorservicos = 100.25;
    $std->valordeducoes = 0.00;
    $std->valorpis = 10.00;
    $std->valorcofins = 10.00;
    $std->valorinss = 10.00;
    $std->valorir = 10.00;
    $std->valorcsll = 10.00;
    $std->aliquota = 0.05;
    $std->issretido = false;
    $std->valortotalrecebido = null;

    $std->valorcargatributaria = 5.00;
    $std->percentualcargatributaria = 0.05;
    $std->fontecargatributaria = 'IBPT';

    $std->tomador = new \stdClass();
    $std->tomador->cnpj = '12345678901234'; //opcional
    //$std->tomador->cpf = '39521777176'; //opcional
    $std->tomador->im = '123456';
    $std->tomador->ie = '12345678909';
    $std->tomador->nome = 'TOMADOR PF';
    $std->tomador->email = 'tomador@uol.com.br';

    $std->tomador->endereco = new \stdClass();
    $std->tomador->endereco->tipologradouro = null;
    $std->tomador->endereco->logradouro = 'Paulista';
    $std->tomador->endereco->numero = '100';
    $std->tomador->endereco->complemento = 'Cj 35';
    $std->tomador->endereco->bairro = 'Bela Vista';
    $std->tomador->endereco->codigoibge = '3550308';
    $std->tomador->endereco->uf = 'SP';
    $std->tomador->endereco->cep = '01310100';

    $std->intermediario = new \stdClass(); //false
    $std->intermediario->cnpj = '99999999000191'; //false 
    $std->intermediario->cpf = null; //false
    $std->intermediario->im = '80417010';
    $std->intermediario->issretido = false;
    $std->intermediario->email = "fulano@mail.com";

    $std->construcaocivil = new \stdClass();
    $std->construcaocivil->codigoobra = '1234';
    $std->construcaocivil->matricula = '1234';
    $std->construcaocivil->municipioprestacao = '3550308';
    $std->construcaocivil->numeroencapsulamento = '1234';
    
    $arps[] = new Rps($std);

    $sincrono = true;
    $response = $tools->recepcionarLoteRps($arps, $sincrono);

    echo FakePretty::prettyPrint($response, '');
    
} catch (\Exception $e) {
    echo $e->getMessage();
}