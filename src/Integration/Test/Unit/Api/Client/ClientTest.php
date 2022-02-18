<?php
namespace Accord\Integration\Api\Client;

use GuzzleHttp\HandlerStack;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ClientTest extends TestCase
{
    /**
     * @test
     * @covers \Accord\Integration\Api\Client\Client::init
     */
    public function testInit()
    {
        /**
         * @var ConfigInterface|MockObject $config
         */
        $config = $this
            ->getMockBuilder('Accord\Integration\Api\Client\ConfigInterface')
            ->setMethods(['getRestApiEndpoint', 'getRestApiUsername', 'getRestApiPassword', 'getHandler'])
            ->getMock();

        $config->expects($this->once())->method('getRestApiEndpoint')->willReturn('http://example.com/__test__');
        $config->expects($this->once())->method('getRestApiUsername')->willReturn('__user__');
        $config->expects($this->once())->method('getRestApiPassword')->willReturn('__pswd__');

        $handler = HandlerStack::create();
        $config->expects($this->once())->method('getHandler')->willReturn($handler);

        $client = new Client();
        $client->init($config);

        $reflectionClient = new \ReflectionClass('Accord\Integration\Api\Client\Client');

        $baseUrl = $reflectionClient->getProperty('baseUrl');
        $baseUrl->setAccessible(true);
        $this->assertEquals($baseUrl->getValue($client), 'http://example.com/__test__');

        $guzzle = $reflectionClient->getProperty('guzzle');
        $guzzle->setAccessible(true);
        $guzzle = $guzzle->getValue($client);

        $this->assertEquals('__user__', $guzzle->getConfig('auth')[0]);
        $this->assertEquals('__pswd__', $guzzle->getConfig('auth')[1]);
        $this->assertEquals($handler, $guzzle->getConfig('handler'));
    }

    /**
     * @test
     * @covers \Accord\Integration\Api\Client\Client::__call
     * @covers \Accord\Integration\Api\Client\Client::applyUrl
     */
    public function testCall()
    {
        $reflectionClient = new \ReflectionClass('Accord\Integration\Api\Client\Client');
        $guzzle = $reflectionClient->getProperty('guzzle');
        $guzzle->setAccessible(true);

        $client = new Client();

        $guzzleMock = $this
            ->getMockBuilder('GuzzleHttp\Client')
            ->setMethods(['__call'])
            ->getMock();

        $params = ['param1' => 1, 'param2' => 2];

        $guzzleMock
            ->expects($this->once())
            ->method('__call')
            ->with($this->callback(function ($name) {
                $this->assertEquals('__MAGIC_METHOD__', $name);
                return true;
            }), $this->callback(function ($args) use ($params) {
                $url = explode('?', $args[0])[0];
                $this->assertEquals('http://example.com/__test__', $url);
                $this->assertEquals($params, $args[1]);
                return true;
            }));

        $guzzle->setValue($client, $guzzleMock);

        $baseUrl = $reflectionClient->getProperty('baseUrl');
        $baseUrl->setAccessible(true);
        $baseUrl->setValue($client, 'http://example.com');

        $client->__MAGIC_METHOD__('__test__', $params);
    }

    /**
     * @test
     * @covers \Accord\Integration\Api\Client\Client::__call
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Magic request methods require a URI and optional options array
     */
    public function testCallInvalidArgumentException()
    {
        $client = new Client();
        $client->post();
    }

    /**
     * @test
     * @covers \Accord\Integration\Api\Client\Client::__call
     * @expectedException \Accord\Integration\Api\Client\ClientException
     * @expectedExceptionMessage __TEST_MESSAGE__
     */
    public function testCallClientException()
    {
        $reflectionClient = new \ReflectionClass('Accord\Integration\Api\Client\Client');
        $guzzle = $reflectionClient->getProperty('guzzle');
        $guzzle->setAccessible(true);

        $client = new Client();

        $guzzleMock = $this
            ->getMockBuilder('GuzzleHttp\Client')
            ->setMethods(['__call'])
            ->getMock();

        $request = $this->getMock('Psr\Http\Message\RequestInterface');

        $guzzleMock
            ->expects($this->once())
            ->method('__call')
            ->willThrowException(new ClientException('__TEST_MESSAGE__', $request));

        $guzzle->setValue($client, $guzzleMock);

        $client->__MAGIC_METHOD__('_ANY_URI_');
    }
}