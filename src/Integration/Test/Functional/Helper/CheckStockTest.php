<?php
namespace Accord\Integration\Test\Functional\Helper;

use Accord\Integration\Api\Request\User;
use Accord\Integration\Helper\CheckStock as Api;
use Accord\Integration\Helper\User as ApiUser;

class CheckStockTest extends \PHPUnit_Framework_TestCase
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
    public function testCheckStock(Api $apiHelper, ApiUser $apiUser)
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
            'cart' => [
                [
                    'id' => 'test1',
                    'productSku' => '184961', // 418310
                    'quantity' => 1,
                    'quantityType' => 'singles',
                ],
                [
                    'id' => 'test2',
                    'productSku' => '184961', // 418310
                    'quantity' => 10,
                    'quantityType' => 'singles',
                ],
                [
                    'id' => 'test3',
                    'productSku' => '418310', // 418310
                    'quantity' => 1,
                    'quantityType' => 'cases',
                ],
            ],
        ];

        /**
         * @var \Accord\Integration\Api\Request\CheckStock $request
         */
        $request = $this->getObjectManager()->get(\Accord\Integration\Api\Request\CheckStock::class);
        $request->setData($data);

        $calculateCart = $apiHelper->checkStock(null, $request);

        $cart = $calculateCart->getCart();

        foreach ($cart as $item) {
            echo $item->productSku . ' - ' . $item->actualStockLevel . "\n";
        }

        $this->assertEquals($calculateCart->getCart()[0]->id, $data['cart'][0]['id']);
        $this->assertEquals($calculateCart->getCart()[1]->id, $data['cart'][1]['id']);

        $item = $calculateCart->getCartItem($data['cart'][1]['id']);
        $this->assertNotEmpty($item);

    }


}