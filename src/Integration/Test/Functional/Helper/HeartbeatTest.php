<?php
namespace Accord\Integration\Test\Functional\Helper;

use Accord\Integration\Test\Env\ApiHelpers;
use Accord\Integration\Test\Env\SettingsExample;
use Accord\Integration\Helper\Heartbeat as Api;
use Accord\Integration\Api\Client\Config;

class HeartbeatTest extends \PHPUnit_Framework_TestCase
{
    use ApiHelpers;
    use SettingsExample;

    /**
     * @return Api
     */
    protected function getPhalcon_Api_401()
    {
        $phalconConfig = $this->getPhalconConfig();
        $phalconConfig['apiPassword'] = 'invalid';
        $config = new Config($phalconConfig);
        return $this->getObjectManager()->create(\Accord\Integration\Helper\Heartbeat::class, ['config' => $config]);
    }

    /**
     * @return \Accord\Integration\Helper\HeartbeatCheck
     */
    protected function getSystem_HeartbeatCheck()
    {
        return $this->getObjectManager()->create(\Accord\Integration\Helper\HeartbeatCheck::class);
    }

    /**
     * @provider
     * @return array[[\Accord\Integration\Helper\Api]]
     */
    public function providerGetHelpers()
    {
        return [
            [$this->getPhalcon_Api(Api::class)],
            [$this->getAccord_Api(Api::class)],
        ];
    }

    /**
     * @test
     * @dataProvider providerGetHelpers
     * @param Api $apiHelper
     */
    public function testHeartbeat(Api $apiHelper)
    {
        $heartbeat = $apiHelper->heartbeat();
        $this->assertEquals($heartbeat->getStatusCode(), 200);
        $this->assertEquals($heartbeat->getBody(), '');
    }

    /**
     * @test
     * @expectedException \Accord\Integration\Api\Client\ClientException
     * @expectedExceptionCode 401
     */
    public function testHeartbeat401()
    {
        $apiHelper = $this->getPhalcon_Api_401();
        $apiHelper->heartbeat();
    }


    /**
     * @test
     */
    public function testHeartbeatCheck()
    {
        $heartbeatCheck = $this->getSystem_HeartbeatCheck();
        $heartbeatCheck->check($this->getPhalconConfig());
    }

}