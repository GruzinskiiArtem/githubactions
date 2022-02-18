<?php
namespace Accord\Integration\Test\Functional\Helper;

use Accord\Integration\Api\Request\User;
use Accord\Integration\Helper\Customer as Api;
use Accord\Integration\Helper\User as ApiUser;
use Accord\Integration\Test\Env\ApiHelpers;

class CustomerProductInfoTest extends \PHPUnit_Framework_TestCase
{
    use ApiHelpers;

    /**
     * @provider
     * @return array[[\Accord\Integration\Helper\Api]]
     */
    public function providerGetHelpers()
    {
        return [
            [$this->getPhalcon_Api(Api::class), $this->getPhalcon_Api(ApiUser::class)],
            [$this->getAccord_Api(Api::class), $this->getAccord_Api(ApiUser::class)],
        ];
    }

    /**
     * @test
     * @dataProvider providerGetHelpers
     * @param Api $apiHelper
     * @param ApiUser $apiUser
     */
    public function testCustomerProductInfo(Api $apiHelper, ApiUser $apiUser)
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
            'productSkus' => ['050670'],
        ];
        $customerProductInfo = $apiHelper->customerProductInfo($data);
        $r = $customerProductInfo->getBody() . '';
        $this->assertNotEmpty($r);
    }

    /**
     * @test
     * @dataProvider providerGetHelpers
     * @param Api $apiHelper
     * @param ApiUser $apiUser
     */
    public function testCustomerProductInfoCache(Api $apiHelper, ApiUser $apiUser)
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
            'productSkus' => ['050670'],
        ];

        $customerProductInfo = $apiHelper->customerProductInfoUseCache($data);
        $r = $customerProductInfo->getItems();
        $this->assertTrue(count($r) > 0);

        sleep(1);

        $customerProductInfo = $apiHelper->customerProductInfoUseCache($data);
        $r = $customerProductInfo->getItems();
        $this->assertTrue(count($r) > 0);

    }


    /**
     * @test
     * @expectedException \Accord\Integration\Api\Response\ResponseException
     */
    public function testCustomerProductInfo422()
    {
        /**
         * @var Api $apiHelper
         * @var ApiUser $apiUser
         */
        $apiHelper = $this->getAccord_Api(Api::class);
        $apiUser = $this->getAccord_Api(ApiUser::class);

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
            'productSkus' => ['am500L'],
        ];
        $customerProductInfo = $apiHelper->customerProductInfo($data);
    }


    /**
     * @test
     * @see https://jira.itransition.com/browse/AM2-260
     */
    public function testCustomerProductInfoABBE16()
    {
        /**
         * @var Api $apiHelper
         * @var ApiUser $apiUser
         */
        $apiHelper = $this->getAccord_Api(Api::class);
        $apiUser = $this->getAccord_Api(ApiUser::class);
        $data = [
            'userCode' => 'ABBE16',
            'userType' => 'Customer',
        ];
        $getUser = $apiUser->getUser($data, new User());

        $this->assertEquals($getUser->getStatusCode(), 200);
        $this->assertNotEmpty($getUser->allowViewingStatements);

        $code = $getUser->getCustomers()[0]->customerCode;

        $data = [
            'customerCode' => $code,
            'productSkus' => ['303731'],
        ];
        $customerProductInfo = $apiHelper->customerProductInfo($data);
        $s = $customerProductInfo->getItems()[0]->getCaseWsp()->nonPromWsp;
        $this->assertNotEmpty($s);
    }

}