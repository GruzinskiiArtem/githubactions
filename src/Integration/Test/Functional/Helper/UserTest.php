<?php
namespace Accord\Integration\Test\Functional\Helper;

use Accord\Integration\Api\Request\User;
use Accord\Integration\Helper\User as Api;
use Accord\Integration\Test\Env\ApiHelpers;

class UserTest extends \PHPUnit_Framework_TestCase
{
    use ApiHelpers;

    /**
     * @provider
     * @return array[[Api]]
     */
    public function providerGetHelpers()
    {
        return [
            [$this->getAccord_Api(Api::class)],
            [$this->getPhalcon_Api(Api::class)],
        ];
    }

    /**
     * @test
     * @dataProvider providerGetHelpers
     * @param Api $apiHelper
     */
    public function testApproveUser(Api $apiHelper)
    {
        $data = [
            'userCode' => '06',
            'userType' => 'Rep',
        ];
        $approveUser = $apiHelper->approveUser($data, new User());
        $this->assertEquals($approveUser->getStatusCode(), 200);
        $this->assertEmpty($approveUser->getBody());
    }

    /**
     * @test
     * @dataProvider providerGetHelpers
     * @param Api $apiHelper
     * @return \Accord\Integration\Api\Response\ApproveUserHeadOffice|\Accord\Integration\Api\Response\ResponseInterface
     */
    public function testApproveUserHeadOffice(Api $apiHelper)
    {
        $data = [
            'userCode' => 'ABBE16',
            'userType' => 'Head Office',
        ];
        $approveUser = $apiHelper->approveUserHeadOffice($data, new User());
        $this->assertEquals($approveUser->getStatusCode(), 200);
        $this->assertNotEmpty($approveUser->headOfficeCode);

        return $approveUser;
    }

    /**
     * @test
     * @dataProvider providerGetHelpers
     * @param Api $apiHelper
     */
    public function testGetUser(Api $apiHelper)
    {
        $data = [
            'userCode' => '221',
            'userType' => 'Customer',
        ];
        $getUser = $apiHelper->getUser($data, new User());
        $this->assertEquals($getUser->getStatusCode(), 200);
        $this->assertNotEmpty($getUser->allowViewingStatements);
    }

    /**
     * @test
     * @dataProvider providerGetHelpers
     * @param Api $apiHelper
     */
    public function testGetUserHeadOffice(Api $apiHelper)
    {
        $r = $this->testApproveUserHeadOffice($apiHelper);
        $data = [
            //'userCode' => 'ABBE16',
            //'userCode' => 'cre,1,ABBE16',
            'userCode' => $r->headOfficeCode,
            'userType' => 'Head Office',
        ];
        $getUser = $apiHelper->getUser($data, new User());
        $this->assertEquals($getUser->getStatusCode(), 200);
        $this->assertNotEmpty($getUser->allowViewingStatements);
    }

    /**
     * @test
     * @dataProvider providerGetHelpers
     * @param Api $apiHelper
     */
    public function testGetUserCache(Api $apiHelper)
    {
        $data = [
            'userCode' => '221',
            'userType' => 'Customer',
        ];
        $request = new User();
        $request->setData($data);
        $getUser = $apiHelper->getUserUseCache($request);
        $this->assertNotEmpty($getUser->allowViewingStatements);
    }

}