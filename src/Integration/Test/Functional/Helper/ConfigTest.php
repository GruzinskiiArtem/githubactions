<?php
namespace Accord\Integration\Test\Functional\Helper;

use Accord\Integration\Helper\ClientConfig;
use Accord\Integration\Test\Env\ObjectManager;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    use ObjectManager;

    /**
     * @var ClientConfig
     */
    protected $config;

    protected function setUp()
    {
        $this->config = $this->getObjectManager()->create('Accord\Integration\Helper\ClientConfig');
    }

    /**
     * @test
     * @covers \Accord\Integration\Helper\ClientConfig::getRestApiEndpoint
     * @covers \Accord\Integration\Helper\ClientConfig::getConfigValue
     */
    public function testGetRestApiEndpoint()
    {
        $apiEndpoint = $this->config->getRestApiEndpoint();
        $this->assertNotEmpty($apiEndpoint);
        $this->assertEquals(filter_var($apiEndpoint, FILTER_VALIDATE_URL), $apiEndpoint);
    }

    /**
     * @test
     * @covers \Accord\Integration\Helper\ClientConfig::getRestApiUsername
     */
    public function testGetRestApiUsername()
    {
        $apiUsername = $this->config->getRestApiUsername();
        $this->assertNotEmpty($apiUsername);
    }

    /**
     * @test
     * @covers \Accord\Integration\Helper\ClientConfig::getRestApiPassword
     */
    public function testGetRestApiPassword()
    {
        $apiPassword = $this->config->getRestApiPassword();
        $this->assertNotEmpty($apiPassword);
    }

}
