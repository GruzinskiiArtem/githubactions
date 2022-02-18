<?php
namespace Accord\Integration\Api\Client;

use GuzzleHttp\HandlerStack;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @test
     * @covers \Accord\Integration\Api\Client\Config::__construct
     * @covers \Accord\Integration\Api\Client\Config::getRestApiEndpoint
     * @covers \Accord\Integration\Api\Client\Config::getRestApiUsername
     * @covers \Accord\Integration\Api\Client\Config::getRestApiPassword
     * @covers \Accord\Integration\Api\Client\Config::getHandler
     */
    public function testAllMethods()
    {
        $config = new Config([
            'apiEndpoint' => '_endpoint',
            'apiUsername' => '_username',
            'apiPassword' => '_password',
        ]);

        $this->assertEquals('_endpoint', $config->getRestApiEndpoint());
        $this->assertEquals('_username', $config->getRestApiUsername());
        $this->assertEquals('_password', $config->getRestApiPassword());

        $this->assertTrue($config->getHandler() instanceof HandlerStack);
    }
}
