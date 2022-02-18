<?php
namespace Accord\Integration\Api\Request;
use PHPUnit\Framework\TestCase;

class RequestExceptionTest extends TestCase
{
    /**
     * @test
     * @covers \Accord\Integration\Api\Request\RequestException::__construct
     * @covers \Accord\Integration\Api\Request\RequestException::getRequest
     */
    public function testGetParams()
    {
        $params = $this->getMock('Accord\Integration\Api\Request\RequestInterface');
        $exception = new RequestException('message', 10, $params);
        $this->assertEquals($params, $exception->getRequest());
    }
}