<?php


namespace Crmdesenvolvimentos\ApiBraspress\Tests;


use Crmdesenvolvimentos\ApiBraspress\Braspress;
use PHPUnit\Framework\TestCase;

class BraspressTest extends TestCase
{

    public function testFreight()
    {
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

        $api->send();

        $this->assertEquals($api->response->getStatusCode(), 200);
        $this->assertEmpty($api->freight->errorList);
    }


    public function testeTracking()
    {
        $api = new Braspress();
        $api
            ->setEnvironment(Braspress::PRODUCTION)
            ->setMode(Braspress::MODE_TRACKING)
            ->setCnpjRemetente('60701190000104')
            ->setUser('user')
            ->setPassword('password');

        $api->tracking->trackingByNfe('12345');

        $api->send();

        $this->assertEquals($api->response->getStatusCode(), 200);
        $this->assertEmpty($api->freight->errorList);
    }

}
