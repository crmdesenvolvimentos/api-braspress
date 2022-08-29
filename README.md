# Api Braspress
Api para cotação de frete e rastreamento junto a Transportadora Braspress


Documentação da api: https://api.braspress.com/home


##Instalação

```php
composer require crmdesenvolvimentos/api-braspress
```


## Realizar cotação de frete

```php
<?php

use Crmdesenvolvimentos\ApiBraspress\Braspress;

$api = new Braspress();
$api
    ->setEnvironment(Braspress::DEVELOPMENT)
    ->setMode(Braspress::MODE_FREIGHT)
    ->setCnpjRemetente('60701190000104')
    ->setUser('user')
    ->setPassword('password');

$api->freight
    ->setCnpjDestinatario('30539356867')
    ->setModal('R')
    ->setTipoFrete(1)
    ->setCepOrigem('02323000')
    ->setCepDestino('07093090')
    ->setVlrMercadoria(100.00)
    ->setPeso(50.00)
    ->setVolumes(100);

$api->freight
    ->getCubagem()
    ->newItem(0.67, 0.67, 0.46, 10);

$response = $api->send();

//Response
Array
(
    'id' => 147670114,                     // id da cotação
    'prazo' => 5,                          // prazo de entrega
    'totalFrete' => 42.14,                 // valor da cotação
    'validade' => '2022-08-29 23:59:59',   // validade da cotação
    'datetime' => '2022-08-29 16:01:34',   // data-hora da requisição
);
```



## Realizar rastreamento de objeto

```php
<?php

use Crmdesenvolvimentos\ApiBraspress\Braspress;

$api = new Braspress();
$api
    ->setEnvironment(Braspress::DEVELOPMENT)
    ->setMode(Braspress::MODE_TRACKING)
    ->setCnpjRemetente('60701190000104')
    ->setUser('user')
    ->setPassword('password');

$api->tracking->trackingByNfe('12345');

//ou

$api->tracking->trackingByNumPedido('123456');

$response = $api->send();

//Response
//api v3, o retorno é o mesmo para as duas requisições de rastreamento
Array
(
    'conhecimentos' => Array(
        0 => Array(
            'statusEntrega' => 'ENTREGUE',
            'numero' => '...',
            'origem' => '...',
            'destino' => '...',
            'emissao' => '...',
            'remetente' => '...',
            'destinatario' => '...',
            'tipoFrete' => '...',
            'volumes' => 1,
            'valorMercantil' => 99999,
            'peso' => 9,
            'totalFrete' => 99.99,
            'previsaoEntrega' => '16/08/2022',
            'dataEntrega' => '18/08/2022',
            'status' => 'FINALIZADO',
            'cidade' => '...',
            'uf' => '...',
            'cidadeColeta' => '...',
            'ufColeta' => '...',
            'dataOcorrencia' => '18/08/2022 03:54',
            'ultimaOcorrencia' => 'ENTREGA REALIZADA',
            'notasFiscais' => Array(
                0 => Array(
                    'serie' => 1,
                    'numero' => 9999999,
                    'emissao' => '28/07/2022',
                )
            ),
            'timeLine' => Array(
                0 => Array(
                    'descricao' => 'Encomenda na Origem (Braspress)',
                    'data' => '29/07/2022'
                )
            ),
        ),
    ),
    'totalNf' => 0,
    'fluxoAtendimento' => null, 
    'datetime' => '2022-08-29 16:10:32'
);
