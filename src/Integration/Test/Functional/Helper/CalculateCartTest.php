<?php
namespace Accord\Integration\Test\Functional\Helper;

use Accord\Integration\Api\Request\User;
use Accord\Integration\Helper\CalculateCart as Api;
use Accord\Integration\Helper\User as ApiUser;

class CalculateCartTest extends \PHPUnit_Framework_TestCase
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
            [$this->getPhalcon_Api(Api::class), $this->getPhalcon_Api(ApiUser::class)],
        ];
    }

    /**
     * @test
     * @dataProvider providerGetHelpers
     * @param Api $apiHelper
     * @param ApiUser $apiUser
     */
    public function testCalculateCart(Api $apiHelper, ApiUser $apiUser)
    {
        $data = [
            'userCode' => '221',
            'userType' => 'Customer',
        ];
        $getUser = $apiUser->getUser($data, new User());

        $this->assertEquals($getUser->getStatusCode(), 200);
        $this->assertNotEmpty($getUser->allowViewingStatements);

        $code = $getUser->getCustomers()[0]->customerCode;

        $deliveryDate = \DateTime::createFromFormat(
            \Accord\Checkout\Block\Cart\Onepage\DeliveryDetails::CREATE_DATE_FORMAT,
            "08/12/2016");

        $data = [
            'customerCode' => $code,
            //'deliveryDate' => date('Y-m-d h:i:s', time() + 100 * 24 * 60 * 60),
            //'deliveryDate' => date('r'),
            'deliveryDate' => $deliveryDate,
            'cart' => [
                [
                    'id' => null,
                    'productSku' => '184961', // 418310
                    'quantity' => 1,
                    'quantityType' => 'singles',
                    'freeLine' => false,
                    // 'mixMatchCode' => 'string', // (optional)
                ],
                [
                    'id' => md5('qwe2'),
                    'productSku' => '184961', // 418310
                    'quantity' => 1,
                    'quantityType' => 'cases',
                    'freeLine' => true,
                    // 'mixMatchCode' => 'string', // (optional)
                ],
            ],
        ];

        /**
         * @var \Accord\Integration\Api\Request\CalculateCart $request
         */
        $request = $this->getObjectManager()->get(\Accord\Integration\Api\Request\CalculateCart::class);
        $request->setData($data);

        $calculateCart = $apiHelper->calculateCart(null, $request);
        $this->assertNotEmpty($calculateCart->cart);
        $this->assertEquals($calculateCart->getCart()[0]->id, $data['cart'][0]['id']);
        $this->assertEquals($calculateCart->getCart()[1]->id, $data['cart'][1]['id']);

        $item = $calculateCart->getCartItem($data['cart'][1]['id']);
        $this->assertNotEmpty($item);
    }


    /**
     * @test
     * @dataProvider providerGetHelpers
     * @param Api $apiHelper
     * @param ApiUser $apiUser
     */
    public function testCalculateCartGuest(Api $apiHelper, ApiUser $apiUser)
    {
        $deliveryDate = \DateTime::createFromFormat(
            \Accord\Checkout\Block\Cart\Onepage\DeliveryDetails::CREATE_DATE_FORMAT,
            "08/12/2016");

        $data = [
            'customerCode' => '',
            'deliveryDate' => $deliveryDate,
            'cart' => [
                [
                    'id' => null,
                    'productSku' => '184961', // 418310
                    'quantity' => 1,
                    'quantityType' => 'singles',
                    'freeLine' => false,
                    // 'mixMatchCode' => 'string', // (optional)
                ],
                [
                    'id' => md5('qwe2'),
                    'productSku' => '184961', // 418310
                    'quantity' => 1,
                    'quantityType' => 'cases',
                    'freeLine' => true,
                    // 'mixMatchCode' => 'string', // (optional)
                ],
            ],
        ];

        /**
         * @var \Accord\Integration\Api\Request\CalculateCart $request
         */
        $request = $this->getObjectManager()->get(\Accord\Integration\Api\Request\CalculateCart::class);
        $request->setData($data);

        $calculateCart = $apiHelper->calculateCart(null, $request);
        $this->assertNotEmpty($calculateCart->cart);
        $this->assertEquals($calculateCart->getCart()[0]->id, $data['cart'][0]['id']);
        $this->assertEquals($calculateCart->getCart()[1]->id, $data['cart'][1]['id']);

        $item = $calculateCart->getCartItem($data['cart'][1]['id']);
        $this->assertNotEmpty($item);
    }


    // 303370. 303380, 303420 and 303450,

    /**
     * @test
     * @dataProvider providerGetHelpers
     * @param Api $apiHelper
     * @param ApiUser $apiUser
     */
    public function testCalculateCart2(Api $apiHelper, ApiUser $apiUser)
    {
        $data = [
            'userCode' => '06',
            'userType' => 'Rep',
        ];
        $getUser = $apiUser->getUser($data, new User());

        $this->assertEquals($getUser->getStatusCode(), 200);
        $this->assertNotEmpty($getUser->allowViewingStatements);

        $code = $getUser->getCustomers()[0]->customerCode;

        $deliveryDate = \DateTime::createFromFormat(
            \Accord\Checkout\Block\Cart\Onepage\DeliveryDetails::CREATE_DATE_FORMAT,
            "08/12/2016");

        $data = [
            'customerCode' => $code,
            'deliveryDate' => $deliveryDate,
            'cart' => [
                [
                    'id' => null,
                    'productSku' => '303370', // 418310
                    'quantity' => 4,
                    'quantityType' => 'cases',
                    'freeLine' => false,
                ],
                [
                    'id' => md5('qwe2'),
                    'productSku' => '303380', // 418310
                    'quantity' => 4,
                    'quantityType' => 'cases',
                    'freeLine' => false,
                ],
                [
                    'id' => md5('qwe3'),
                    'productSku' => '303420', // 418310
                    'quantity' => 4,
                    'quantityType' => 'cases',
                    'freeLine' => false,
                ],
                [
                    'id' => md5('qwe4'),
                    'productSku' => '303450', // 418310
                    'quantity' => 4,
                    'quantityType' => 'cases',
                    'freeLine' => false,
                ],
            ],
        ];

        /**
         * @var \Accord\Integration\Api\Request\CalculateCart $request
         */
        $request = $this->getObjectManager()->get(\Accord\Integration\Api\Request\CalculateCart::class);
        $request->setData($data);

        $calculateCart = $apiHelper->calculateCart(null, $request);

        $data1 = \json_encode($request->getData());
        $data2 = \json_encode($calculateCart->getData());

        echo "\n request:";
        echo $data1;
        echo "\n\n response:";
        echo $data2;
        echo "\n";

        $this->assertNotEmpty($calculateCart->cart);
        $this->assertEquals($calculateCart->getCart()[0]->id, $data['cart'][0]['id']);
        $this->assertEquals($calculateCart->getCart()[1]->id, $data['cart'][1]['id']);
        $this->assertEquals($calculateCart->getCart()[3]->id, $data['cart'][3]['id']);

        $item = $calculateCart->getCartItem($data['cart'][1]['id']);
        $this->assertNotEmpty($item);
    }

    /*
    [2017-01-31 10:23:07] AccordIntegration.rest.INFO: >>>>>>>>
    POST /am2/rest/api/magento/calculateCart HTTP/1.1
    Content-Length: 395 User-Agent: GuzzleHttp/6.2.1 curl/7.35.0 PHP/7.0.10-2+deb.sury.org~trusty+1$
    */

    public function testCalculateCartAmazon()
    {
        $apiHelper = $this->getAmazon_Api(Api::class);
        $apiUser = $this->getAmazon_Api(ApiUser::class);

        $data = [
            'userCode' => 'web002',
            'userType' => 'Customer',
        ];
        $getUser = $apiUser->getUser($data, new User());

        $this->assertEquals($getUser->getStatusCode(), 200);
        $this->assertNotEmpty($getUser->allowViewingStatements);

        $code = $getUser->getCustomers()[0]->customerCode;

        $deliveryDate = \DateTime::createFromFormat(
            \Accord\Checkout\Block\Cart\Onepage\DeliveryDetails::CREATE_DATE_FORMAT,
            "01/02/2017");

        $data = [
            'customerCode' => $code,
            //'deliveryDate' => date('Y-m-d h:i:s', time() + 100 * 24 * 60 * 60),
            //'deliveryDate' => date('r'),
            'deliveryDate' => $deliveryDate,
            'cart' => [
                [
                    'id' => '01',
                    'productSku' => 'NEU001',
                    'quantity' => 4,
                    'quantityType' => 'cases',
                    'freeLine' => false,
                    // 'mixMatchCode' => 'string', // (optional)
                ],
                [
                    'id' => '01',
                    'productSku' => 'NEU003',
                    'quantity' => 4,
                    'quantityType' => 'cases',
                    'freeLine' => false,
                    // 'mixMatchCode' => 'string', // (optional)
                ],
                [
                    'id' => '02',
                    'productSku' => 'NEU004',
                    'quantity' => 4,
                    'quantityType' => 'cases',
                    'freeLine' => false,
                    // 'mixMatchCode' => 'string', // (optional)
                ],
            ],
        ];

        /**
         * @var \Accord\Integration\Api\Request\CalculateCart $request
         */
        $request = $this->getObjectManager()->get(\Accord\Integration\Api\Request\CalculateCart::class);
        $request->setData($data);

        $calculateCart = $apiHelper->calculateCart(null, $request);
        $this->assertNotEmpty($calculateCart->cart);
        $this->assertEquals($calculateCart->getCart()[0]->id, $data['cart'][0]['id']);
        $this->assertEquals($calculateCart->getCart()[1]->id, $data['cart'][1]['id']);

        $item = $calculateCart->getCartItem($data['cart'][1]['id']);
        $this->assertNotEmpty($item);
    }


}