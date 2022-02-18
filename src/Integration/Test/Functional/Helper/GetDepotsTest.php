<?php

namespace Accord\Integration\Test\Functional\Helper;

class GetDepotsTest extends \PHPUnit_Framework_TestCase
{
    use \Accord\Integration\Test\Env\ApiHelpers;

    /**
     * @provider
     * @return array[[\Accord\Integration\Helper\Api]]
     */
    public function providerGetHelpers()
    {
        return [
            /*[
                $this->getPhalcon_Api(\Accord\Integration\Helper\DepotProcessor::class),
                $this->getPhalcon_Api(\Accord\Integration\Helper\DepotProcessor::class)
            ],*/
            [
                $this->getAccord_Api(\Accord\Integration\Helper\DepotProcessor::class),
                $this->getAccord_Api(\Accord\Integration\Helper\DepotProcessor::class)
            ],
        ];
    }

    /**
     * @test
     * @dataProvider providerGetHelpers
     * @param \Accord\Integration\Helper\DepotProcessor $apiHelper
     */
    public function testGetDepots(
        \Accord\Integration\Helper\DepotProcessor $apiHelper
    ) {
        $getDepot = $apiHelper->getDepots();
        $this->assertEquals($getDepot->getStatusCode(), 200);
    }
}
