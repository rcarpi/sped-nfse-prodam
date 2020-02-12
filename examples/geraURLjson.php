<?php

$sincrono_urlhom = "https://nfe.prefeitura.sp.gov.br/ws/lotenfe.asmx";
$sincrono_urlprod = "https://nfe.prefeitura.sp.gov.br/ws/lotenfe.asmx";
$assincrono_urlhom = "https://nfews.prefeitura.sp.gov.br/lotenfeasync.asmx";
$assincrono_urlprod = "https://nfews.prefeitura.sp.gov.br/lotenfeasync.asmx";

$msgns = "http://www.prefeitura.sp.gov.br/nfe";
$soapns = "http://www.prefeitura.sp.gov.br/nfe";

$muns = [
    ['ibge' => '3550308', 'mun' => 'São Paulo', 'uf' => 'SP']
];

$am = [];
foreach ($muns as $m) {
    $am["{$m['ibge']}"] = [
        "municipio"                 => $m['mun'],
        "uf"                        => $m['uf'],
        "sincrono_homologacao"      => $sincrono_urlhom,
        "sincrono_producao"         => $sincrono_urlprod,
        "assincrono_homologacao"    => $assincrono_urlhom,
        "assincrono_producao"       => $assincrono_urlprod,
        "version"                   => "1",
        "msgns"                     => $msqns,
        "soapns"                    => $soapns
    ];
}

file_put_contents('../storage/urls_webservices.json', json_encode($am, JSON_PRETTY_PRINT));
