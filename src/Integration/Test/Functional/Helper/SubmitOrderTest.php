<?php
namespace Accord\Integration\Test\Functional\Helper;

use Accord\Integration\Api\Request\User;
use Accord\Integration\Helper\SubmitOrder as Api;
use Accord\Integration\Helper\User as ApiUser;

class SubmitOrderTest extends \PHPUnit_Framework_TestCase
{
    use \Accord\Integration\Test\Env\ApiHelpers;

    /**
     * @provider
     * @return array[[Api,ApiUser]]
     */
    public function providerGetHelpers()
    {
        return [
            [$this->getAccord_Api(Api::class), $this->getAccord_Api(ApiUser::class)],
            [$this->getPhalcon_Api(Api::class), $this->getAccord_Api(ApiUser::class)],
        ];
    }

    /**
     * @test
     * @dataProvider providerGetHelpers
     * @param Api $apiHelper
     * @param ApiUser $apiUser
     */
    public function testSubmitOrder(Api $apiHelper, ApiUser $apiUser)
    {
        $data = [
            'userCode' => '221',
            'userType' => 'Customer',
        ];
        $getUser = $apiUser->getUser($data, new User());

        $this->assertEquals($getUser->getStatusCode(), 200);
        $this->assertNotEmpty($getUser->allowViewingStatements);

        $code = $getUser->getCustomers()[0]->customerCode;

        $data = [
            'customerCode' => $code,
            'deliveryDate' => date('r'),
            'user' => '1',
            'paymentMethod' => 'checkmo',
            'paymentAmount' => 150.0,
            'cart' => [
                [
                    'productSku' => '184961', // 418310
                    'quantity' => 1,
                    'quantityType' => 'singles',
                    'freeLine' => false,
                ],
                [
                    'productSku' => '184961', // 418310
                    'quantity' => 10,
                    'quantityType' => 'singles',
                    'freeLine' => false,
                ],
                [
                    'productSku' => '418310', // 418310
                    'quantity' => 1,
                    'quantityType' => 'cases',
                    'freeLine' => false,
                ],
            ],
        ];

        /**
         * @var \Accord\Integration\Api\Request\SubmitOrder $request
         */
        $request = $this->getObjectManager()->get(\Accord\Integration\Api\Request\SubmitOrder::class);
        $request->setData($data);

        $calculateCart = $apiHelper->submitOrder(null, $request);

        $orderNumber = $calculateCart->orderNumber;

        $this->assertNotEmpty($orderNumber);

        echo $orderNumber;
    }


}