<?php

namespace Accord\Integration\Helper;

use Accord\General\Helper\Config\Catalog\Product;
use Accord\Integration\Api\CacheInterface;
use Accord\Integration\Api\Client\ClientInterface;
use Accord\Integration\Api\Client\ConfigInterface;
use Accord\Integration\Api\Commands\CustomerProductInfo;
use Accord\Integration\Api\Request\RequestInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;

/**
 * @link https://wiki.itransition.com/display/AM2/Customer+Specific+Product+Information
 */
class Customer extends \Accord\Integration\Helper\Api
{
    const CACHE_PREFIX = 'currentCustomerProductInfo:';
    const CACHE_LIFETIME = 60;

    /**
     * @var Product
     */
    protected $productHelper;

    /**
     * Customer constructor.
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param CacheInterface $cache
     * @param ClientInterface $client
     * @param ConfigInterface $config
     * @param Registry $registry
     * @param Product $productHelper
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        CacheInterface $cache,
        ClientInterface $client,
        ConfigInterface $config,
        Registry $registry,
        Product $productHelper
    ) {
        parent::__construct(
            $context,
            $objectManager,
            $cache,
            $client,
            $config,
            $registry
        );

        $this->productHelper = $productHelper;
    }

    /**
     * @param array|null $data
     * @param RequestInterface|null $request
     * @return \Accord\Integration\Api\Response\CustomerProductInfo | \Accord\Integration\Api\Response\ResponseInterface
     */
    public function customerProductInfo($data, RequestInterface $request = null)
    {
        if (!$request) {
            $request = $this->objectManager->get(\Accord\Integration\Api\Request\CustomerProductInfo::class);
        }
        $response = $this->objectManager->get(\Accord\Integration\Api\Response\CustomerProductInfo::class);
        return (new CustomerProductInfo($this->client, $request, $response))->execute($data);
    }

    /**
     * @param array $data
     * @param bool $updateCache
     * @return \Accord\Integration\Api\Response\CustomerProductInfo | \Accord\Integration\Api\Response\ResponseInterface
     */
    public function customerProductInfoUseCache(array $data, $updateCache = false)
    {
        /**
         * @var \Accord\Integration\Api\Request\CustomerProductInfo $request
         */
        $request = $this->objectManager->get(\Accord\Integration\Api\Request\CustomerProductInfo::class);
        $request->setData($data);

        return $this->useCache('customerProductInfo', $request, self::CACHE_PREFIX, self::CACHE_LIFETIME, $updateCache);
    }

    /**
     * @param array $skus
     * @return \Accord\Integration\Api\Response\CustomerProductInfo | null
     */
    public function customerProductInfoBySkus(array $skus)
    {
        /** @var \Accord\Customer\Helper\Current\User $currentUser */
        $currentUser = $this->objectManager->create(\Accord\Customer\Helper\Current\User::class);
        $customerCode = $currentUser->getDataManager()->getCustomerCode();
        $pickupMethod = $currentUser->getDataManager()->getPickupMethod();

        $data = [
            'customerCode' => $customerCode,
            'productSkus' => $skus,
        ];

        if ($this->productHelper->isShowAllProducts() && $currentUser->hasInitialGuestVisit()) {
            $data['allProductsForGuests'] = 'true';
        }

        if (
            $pickupMethod &&
            \Accord\Customer\Helper\Customer\Customer::ACCORD_PICKUP_METHOD_BOTH !== $pickupMethod
        ) {
            $data['pickupMethod'] = $pickupMethod;
        }

        if ($depot = $currentUser->getDataManager()->getDepot()) {
            $data['depot'] = $depot;
        }

        return $this->customerProductInfoUseCache($data);
    }
}
