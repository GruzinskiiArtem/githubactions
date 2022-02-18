<?php

namespace Accord\Integration\Helper;

use Magento\Framework\Registry;

/**
 * @see https://wiki.itransition.com/display/AM2/Calculate+Cart
 */
class CheckStock extends \Accord\Integration\Helper\Api
{
    const CACHE_PREFIX = 'checkStock:';
    const CACHE_LIFETIME = 60;

    /**
     * @var \Accord\Customer\Helper\Current\User
     */
    protected $currentUser;

    /**
     * CheckStock constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Accord\Integration\Api\CacheInterface $cache
     * @param \Accord\Integration\Api\Client\ClientInterface $client
     * @param \Accord\Integration\Api\Client\ConfigInterface $config
     * @param Registry $registry
     * @param \Accord\Customer\Helper\Current\User $currentUser
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Accord\Integration\Api\CacheInterface $cache,
        \Accord\Integration\Api\Client\ClientInterface $client,
        \Accord\Integration\Api\Client\ConfigInterface $config,
        Registry $registry,
        \Accord\Customer\Helper\Current\User $currentUser
    ) {
        parent::__construct(
            $context,
            $objectManager,
            $cache,
            $client,
            $config,
            $registry
        );

        $this->currentUser = $currentUser;
    }

    /**
     * @param array|null $data
     * @param \Accord\Integration\Api\Request\RequestInterface $request
     * @return \Accord\Integration\Api\Response\CheckStock | \Accord\Integration\Api\Response\ResponseInterface
     */
    public function checkStock($data, \Accord\Integration\Api\Request\RequestInterface $request = null)
    {
        if (!$request) {
            $request = $this->objectManager->get(\Accord\Integration\Api\Request\CheckStock::class);
        }
        $response = $this->objectManager->get(\Accord\Integration\Api\Response\CheckStock::class);
        return (new \Accord\Integration\Api\Commands\CheckStock($this->client, $request, $response))->execute($data);
    }

    /**
     * @param \Accord\Integration\Api\Request\RequestInterface $request
     * @return \Accord\Integration\Api\Response\CheckStock
     */
    public function checkStockUseCache(\Accord\Integration\Api\Request\RequestInterface $request)
    {
        /**
         * @see \Accord\Integration\Helper\CheckStock::checkStock
         */
        return $this->useCache('checkStock', $request, self::CACHE_PREFIX, self::CACHE_LIFETIME);
    }

    /**
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @param null $depotCode
     * @return \Accord\Integration\Api\Response\CheckStock
     */
    public function checkStockForSelectedCustomer(\Magento\Quote\Api\Data\CartInterface $quote, $depotCode = null)
    {
        /**
         * @var \Accord\Integration\Api\Request\CheckStockObject $request
         */
        $request = $this->objectManager->get(\Accord\Integration\Api\Request\CheckStockObject::class);

        $data = $this->getRequestData($quote, $depotCode);

        $request->setData($data);
        return $this->checkStockUseCache($request);
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item []
     * @param null $depotCode
     * @return \Accord\Integration\Api\Response\CheckStock
     */
    public function checkStockForForSelectedQuoteItems(array $quoteItems, $depotCode = null)
    {
        /**
         * @var \Accord\Integration\Api\Request\CheckStockData $request
         */
        $request = $this->objectManager->get(\Accord\Integration\Api\Request\CheckStockData::class);

        $data = $this->getRequestData($quoteItems, $depotCode);

        $request->setData($data);

        return $this->checkStockUseCache($request);
    }

    /**
     * @param $data
     * @param $depotCode
     * @return array
     */
    public function getRequestData($data, $depotCode): array
    {
        $customerCode = $this->currentUser->getDataManager()->getCustomerCode();
        $depot = $depotCode ?: $this->currentUser->getDataManager()->getDepot();

        $requestData = [
            $data,
            $customerCode,
            null
        ];

        if ($depot) {
            $requestData = [
                $data,
                $customerCode,
                $depot,
            ];
        }

        return $requestData;
    }

}
