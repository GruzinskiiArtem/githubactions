<?php

namespace Accord\Integration\Test\Functional\Helper;

class PaymentStatusTest extends \PHPUnit_Framework_TestCase
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
                $this->getPhalcon_Api(\Accord\Integration\Helper\PaymentStatus::class),
                $this->getPhalcon_Api(\Accord\Integration\Helper\PaymentStatus::class)
            ],*/
            [
                $this->getAccord_Api(\Accord\Integration\Helper\PaymentProcessor::class),
                $this->getAccord_Api(\Accord\Integration\Helper\PaymentProcessor::class)
            ],
        ];
    }

    /**
     * @test
     * @dataProvider providerGetHelpers
     * @param \Accord\Integration\Helper\PaymentProcessor $apiHelper
     */
    public function testPaymentStatus(
        \Accord\Integration\Helper\PaymentProcessor $apiHelper
    ) {
        $data = [
            'depot' => '1',
            'orderNumber' => '481100',
            'createdDate' => '2017-04-20T10:47:24.513+01:00',
            'status' => \Accord\Integration\Helper\PaymentProcessor::STATUS_SUCCESS,
            'paymentRef' => 'S/123456789012345B'
        ];

        $paymentStatus = $apiHelper->sendPaymentStatus($data);

        $this->assertEquals($paymentStatus->getStatusCode(), 200);
    }
}
