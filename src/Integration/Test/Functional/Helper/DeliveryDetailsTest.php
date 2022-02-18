<?php
namespace Accord\Integration\Test\Functional\Helper;

use Accord\Integration\Api\Request\User;
use Accord\Integration\Helper\DeliveryDetails as Api;
use Accord\Integration\Helper\User as ApiUser;

class DeliveryDetailsTest extends \PHPUnit_Framework_TestCase
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
    public function testDeliveryDetails(Api $apiHelper, ApiUser $apiUser)
    {
        $data = [
            'userCode' => '221',
            'userType' => 'Customer',
        ];
        $request = new User();
        $request->setData($data);
        $getUser = $apiUser->getUserUseCache($request);

        $this->assertEquals($getUser->getStatusCode(), 200);
        $this->assertNotEmpty($getUser->allowViewingStatements);

        $code = $getUser->getCustomers()[0]->customerCode;

        $data = [
            'customerCode' => $code,
        ];

        $getDeliveryDetailsUseCache = $apiHelper->getDeliveryDetails($data);

        $deliveryAddress = $getDeliveryDetailsUseCache->deliveryAddress;
        $defaultDeliveryDate = $getDeliveryDetailsUseCache->defaultDeliveryDate;
        $deliveryDatesAvailable = $getDeliveryDetailsUseCache->deliveryDatesAvailable;
        $this->assertNotEmpty($deliveryAddress);


        $getDeliveryDetailsUseCache = $apiHelper->getDeliveryDetailsUseCache($data);

        $deliveryAddress = $getDeliveryDetailsUseCache->deliveryAddress;
        $defaultDeliveryDate = $getDeliveryDetailsUseCache->defaultDeliveryDate;
        $deliveryDatesAvailable = $getDeliveryDetailsUseCache->deliveryDatesAvailable;
        $this->assertNotEmpty($deliveryAddress);
    }


}