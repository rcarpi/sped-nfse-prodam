# Standarize

## Retorno com Alerta

```
stdClass Object
(
    [Cabecalho] => stdClass Object
        (
            [attributes] => stdClass Object
                (
                    [Versao] => 1
                )

            [Sucesso] => true
        )

    [Alerta] => stdClass Object
        (
            [Codigo] => 1106
            [Descricao] => NFS-e não encontrada.
            [ChaveNFe] => stdClass Object
                (
                    [InscricaoPrestador] => 89444222
                    [NumeroNFe] => 99992866
                )

        )

)
``` 




## Retorno Consulta de CNPJ

```
stdClass Object
(
    [Cabecalho] => stdClass Object
        (
            [attributes] => stdClass Object
                (
                    [Versao] => 1
                )

            [Sucesso] => true
        )

    [Detalhe] => stdClass Object
        (
            [InscricaoMunicipal] => 89444222
            [EmiteNFe] => true
        )

)
```

## Retorno ConsultaInformacoesLote

```
stdClass Object
(
    [Cabecalho] => stdClass Object
        (
            [attributes] => stdClass Object
                (
                    [Versao] => 1
                )

            [Sucesso] => true
            [InformacoesLote] => stdClass Object
                (
                    [NumeroLote] => 555503838
                    [InscricaoPrestador] => 89444222
                    [CPFCNPJRemetente] => stdClass Object
                        (
                            [CNPJ] => 12345678901234
                        )

                    [DataEnvioLote] => 2019-12-02T10:36:10
                    [QtdNotasProcessadas] => 22
                    [TempoProcessamento] => 1
                    [ValorTotalServicos] => 18128.77
                )

        )

)
```


## Retorno ConsultaLoteRps

```
stdClass Object
(
    [Cabecalho] => stdClass Object
        (
            [attributes] => stdClass Object
                (
                    [Versao] => 1
                )

            [Sucesso] => true
        )

    [NFe] => Array
        (
            [0] => stdClass Object
                (
                    [ChaveNFe] => stdClass Object
                        (
                            [InscricaoPrestador] => 89444222
                            [NumeroNFe] => 2865
                            [CodigoVerificacao] => MFT8YKDR
                        )

                    [DataEmissaoNFe] => 2019-12-02T10:36:10
                    [NumeroLote] => 555503838
                    [ChaveRPS] => stdClass Object
                        (
                            [InscricaoPrestador] => 89444222
                            [SerieRPS] => stdClass Object
                                (
                                )

                            [NumeroRPS] => 2253
                        )

                    [TipoRPS] => RPS
                    [DataEmissaoRPS] => 2019-12-02
                    [CPFCNPJPrestador] => stdClass Object
                        (
                            [CNPJ] => 12345678901234
                        )

                    [RazaoSocialPrestador] => PLANET REDE COMERCIO IMPORTACAO E SERVICOS EM INFORMATICA LT
                    [EnderecoPrestador] => stdClass Object
                        (
                            [TipoLogradouro] => R
                            [Logradouro] => MANUEL GAYA
                            [NumeroEndereco] => 00240
                            [Bairro] => TREMEMBE
                            [Cidade] => 3550308
                            [UF] => SP
                            [CEP] => 2313000
                        )

                    [EmailPrestador] => emerson@planetrede.com.br
                    [StatusNFe] => C
                    [DataCancelamento] => 2020-01-02T10:36:32
                    [TributacaoNFe] => T
                    [OpcaoSimples] => 4
                    [ValorServicos] => 1680
                    [CodigoServico] => 2919
                    [AliquotaServicos] => 0
                    [ValorISS] => 0
                    [ValorCredito] => 0
                    [ISSRetido] => false
                    [CPFCNPJTomador] => stdClass Object
                        (
                            [CNPJ] => 02756383000158
                        )

                    [RazaoSocialTomador] => CIA SAO PAULO LANCAMENTOS IMOBILIARIOS LTDA
                    [EnderecoTomador] => stdClass Object
                        (
                            [TipoLogradouro] => AL
                            [Logradouro] => RIO NEGRO
                            [NumeroEndereco] => 161
                            [ComplementoEndereco] => ANDAR 9 CONJ 904
                            [Bairro] => ALPHAVILLE INDUSTRIAL
                            [Cidade] => 3505708
                            [UF] => SP
                            [CEP] => 6454000
                        )

                    [EmailTomador] => financeiro@imovel-cia.com.br
                    [Discriminacao] => . Referente Ã s melhorias no TOOLS. Parcela: 003/003 - Vencimento: 05/12/2019 Carga TributÃ¡ria aproximada Incidente sobre NFs, conforme Lei Federal 12.741/12: 
                            INSS/CPP............ 11,00 % ........ R$    184,80
                            ISS................. 5,00 % ........ R$     84,00
                            Pis/Pasep........... 0,65 % ........ R$     10,92
                            Cofins.............. 3,00 % ........ R$     50,40
                            Total............... 19,65 % ........ R$    330,12
                    [FonteCargaTributaria] => stdClass Object
                    (
                    )
                )
        )    
)    
```

## Retorno ConsultarNfse

```
stdClass Object
(
    [Cabecalho] => stdClass Object
        (
            [attributes] => stdClass Object
                (
                    [Versao] => 1
                )

            [Sucesso] => true
        )

    [NFe] => stdClass Object
        (
            [ChaveNFe] => stdClass Object
                (
                    [InscricaoPrestador] => 89444222
                    [NumeroNFe] => 2866
                    [CodigoVerificacao] => ICTPDJLA
                )

            [DataEmissaoNFe] => 2019-12-02T10:36:10
            [NumeroLote] => 555503838
            [ChaveRPS] => stdClass Object
                (
                    [InscricaoPrestador] => 89444222
                    [SerieRPS] => stdClass Object
                        (
                        )

                    [NumeroRPS] => 2254
                )

            [TipoRPS] => RPS
            [DataEmissaoRPS] => 2019-12-02
            [CPFCNPJPrestador] => stdClass Object
                (
                    [CNPJ] => 12345678901234
                )

            [RazaoSocialPrestador] => PLANET REDE COMERCIO IMPORTACAO E SERVICOS EM INFORMATICA LT
            [EnderecoPrestador] => stdClass Object
                (
                    [TipoLogradouro] => R
                    [Logradouro] => MANUEL GAYA
                    [NumeroEndereco] => 00240
                    [Bairro] => TREMEMBE
                    [Cidade] => 3550308
                    [UF] => SP
                    [CEP] => 2313000
                )

            [EmailPrestador] => emerson@planetrede.com.br
            [StatusNFe] => N
            [TributacaoNFe] => T
            [OpcaoSimples] => 4
            [ValorServicos] => 400
            [CodigoServico] => 2919
            [AliquotaServicos] => 0
            [ValorISS] => 0
            [ValorCredito] => 0
            [ISSRetido] => false
            [CPFCNPJTomador] => stdClass Object
                (
                    [CNPJ] => 02756383000158
                )

            [RazaoSocialTomador] => CIA SAO PAULO LANCAMENTOS IMOBILIARIOS LTDA
            [EnderecoTomador] => stdClass Object
                (
                    [TipoLogradouro] => AL
                    [Logradouro] => RIO NEGRO
                    [NumeroEndereco] => 161
                    [ComplementoEndereco] => ANDAR 9 CONJ 904
                    [Bairro] => ALPHAVILLE INDUSTRIAL
                    [Cidade] => 3505708
                    [UF] => SP
                    [CEP] => 6454000
                )

            [EmailTomador] => financeiro@imovel-cia.com.br
            [Discriminacao] => . Projeto CIA52 â¿¿ VILLAGIO MONTE MOR. Parcela: 001/001 - Vencimento: 15/12/2019 
                Carga TributÃ¡ria aproximada Incidente sobre NFs, conforme Lei Federal 12.741/12:
                INSS/CPP............ 11,00 % ........ R$     44,00
                ISS................. 5,00 % ........ R$     20,00
                Pis/Pasep........... 0,65 % ........ R$      2,60
                Cofins.............. 3,00 % ........ R$     12,00
                Total............... 19,65 % ........ R$     78,60
            [FonteCargaTributaria] => stdClass Object
                (
                )

        )

)
```

## Reposta ConsultarNfsePeriodo

```
stdClass Object
(
    [Cabecalho] => stdClass Object
        (
            [attributes] => stdClass Object
                (
                    [Versao] => 1
                )

            [Sucesso] => true
        )

    [NFe] => Array
        (
            [0] => stdClass Object
                (
                    [ChaveNFe] => stdClass Object
                        (
                            [InscricaoPrestador] => 89444222
                            [NumeroNFe] => 2866
                            [CodigoVerificacao] => ICTPDJLA
                        )

                    [DataEmissaoNFe] => 2019-12-02T10:36:10
                    [NumeroLote] => 555503838
                    [ChaveRPS] => stdClass Object
                        (
                            [InscricaoPrestador] => 89444222
                            [SerieRPS] => stdClass Object
                                (
                                )

                            [NumeroRPS] => 2254
                        )

                    [TipoRPS] => RPS
                    [DataEmissaoRPS] => 2019-12-02
                    [CPFCNPJPrestador] => stdClass Object
                        (
                            [CNPJ] => 12345678901234
                        )

                    [RazaoSocialPrestador] => PLANET REDE COMERCIO IMPORTACAO E SERVICOS EM INFORMATICA LT
                    [EnderecoPrestador] => stdClass Object
                        (
                            [TipoLogradouro] => R
                            [Logradouro] => MANUEL GAYA
                            [NumeroEndereco] => 00240
                            [Bairro] => TREMEMBE
                            [Cidade] => 3550308
                            [UF] => SP
                            [CEP] => 2313000
                        )

                    [EmailPrestador] => emerson@planetrede.com.br
                    [StatusNFe] => N
                    [TributacaoNFe] => T
                    [OpcaoSimples] => 4
                    [ValorServicos] => 400
                    [CodigoServico] => 2919
                    [AliquotaServicos] => 0
                    [ValorISS] => 0
                    [ValorCredito] => 0
                    [ISSRetido] => false
                    [CPFCNPJTomador] => stdClass Object
                        (
                            [CNPJ] => 02756383000158
                        )

                    [RazaoSocialTomador] => CIA SAO PAULO LANCAMENTOS IMOBILIARIOS LTDA
                    [EnderecoTomador] => stdClass Object
                        (
                            [TipoLogradouro] => AL
                            [Logradouro] => RIO NEGRO
                            [NumeroEndereco] => 161
                            [ComplementoEndereco] => ANDAR 9 CONJ 904
                            [Bairro] => ALPHAVILLE INDUSTRIAL
                            [Cidade] => 3505708
                            [UF] => SP
                            [CEP] => 6454000
                        )

                    [EmailTomador] => financeiro@imovel-cia.com.br
                    [Discriminacao] => . Projeto CIA52 â¿¿ VILLAGIO MONTE MOR. Parcela: 001/001 - Vencimento: 15/12/2019
                        Carga TributÃ¡ria aproximada Incidente sobre NFs, conforme Lei Federal 12.741/12:
                        INSS/CPP............ 11,00 % ........ R$     44,00
                        ISS................. 5,00 % ........ R$     20,00
                        Pis/Pasep........... 0,65 % ........ R$      2,60
                        Cofins.............. 3,00 % ........ R$     12,00
                        Total............... 19,65 % ........ R$     78,60
                    [FonteCargaTributaria] => stdClass Object
                        (
                        )

                )
        )
)
```

## Reposta ConsultarNfseRecebidas

```
stdClass Object
(
    [Cabecalho] => stdClass Object
        (
            [attributes] => stdClass Object
                (
                    [Versao] => 1
                )

            [Sucesso] => true
        )

    [NFe] => stdClass Object
        (
            [ChaveNFe] => stdClass Object
                (
                    [InscricaoPrestador] => 10925139
                    [NumeroNFe] => 7540434
                    [CodigoVerificacao] => DBTFZXQF
                )

            [DataEmissaoNFe] => 2019-12-09T10:15:48
            [NumeroLote] => 558792091
            [ChaveRPS] => stdClass Object
                (
                    [InscricaoPrestador] => 10925139
                    [SerieRPS] => 00006
                    [NumeroRPS] => 273556
                )

            [TipoRPS] => RPS
            [DataEmissaoRPS] => 2019-12-01
            [CPFCNPJPrestador] => stdClass Object
                (
                    [CNPJ] => 60975174000100
                )

            [RazaoSocialPrestador] => ASSOCIACAO DE BENEFICENCIA E FILANTROPIA SAO CRISTOVAO
            [EnderecoPrestador] => stdClass Object
                (
                    [TipoLogradouro] => R
                    [Logradouro] => AMERICO VENTURA
                    [NumeroEndereco] => 00123
                    [Bairro] => ALTO DA MOOCA
                    [Cidade] => 3550308
                    [UF] => SP
                    [CEP] => 3128020
                )

            [StatusNFe] => N
            [TributacaoNFe] => M
            [OpcaoSimples] => 0
            [ValorServicos] => 1562.8
            [CodigoServico] => 2097
            [AliquotaServicos] => 0.05
            [ValorISS] => 78.14
            [ValorCredito] => 0
            [ISSRetido] => false
            [CPFCNPJTomador] => stdClass Object
                (
                    [CNPJ] => 12345678901234
                )

            [InscricaoMunicipalTomador] => 89444222
            [RazaoSocialTomador] => PLANET REDE COMERCIO IMPORTACAO E SERVICOS EM INFORMATICA LT
            [EnderecoTomador] => stdClass Object
                (
                    [TipoLogradouro] => R
                    [Logradouro] => MANUEL GAYA
                    [NumeroEndereco] => 00240
                    [Bairro] => TREMEMBE
                    [Cidade] => 3550308
                    [UF] => SP
                    [CEP] => 2313000
                )

            [EmailTomador] => emerson@planetrede.com.br
            [Discriminacao] => O DE ASSISTENCIA MEDICA/PLANO DE SAUDE *** VALOR APROXIMADO DOS TRIBUTOS (ISENTO): R$0,00 - 01/12/2019
            [FonteCargaTributaria] => stdClass Object
                (
                )

        )

)
```

## Resposta TestRecepionarLoteRps

```
stdClass Object
(
    [Cabecalho] => stdClass Object
        (
            [attributes] => stdClass Object
                (
                    [Versao] => 1
                )

            [Sucesso] => true
            [InformacoesLote] => stdClass Object
                (
                    [NumeroLote] => 0
                    [InscricaoPrestador] => 89444222
                    [CPFCNPJRemetente] => stdClass Object
                        (
                            [CNPJ] => 12345678901234
                        )

                    [DataEnvioLote] => 2020-02-26T19:02:11
                    [QtdNotasProcessadas] => 1
                    [TempoProcessamento] => 0
                    [ValorTotalServicos] => 1
                )

        )

)
```

