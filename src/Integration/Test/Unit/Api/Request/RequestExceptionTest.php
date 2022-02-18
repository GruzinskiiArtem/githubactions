<?php
namespace Accord\Integration\Api\Request;

class RequestExceptionTest extends \PHPUnit_Framework_TestCase
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