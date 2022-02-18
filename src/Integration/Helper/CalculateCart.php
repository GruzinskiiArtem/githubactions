<?php

namespace Accord\Integration\Helper;

use Accord\Integration\Api\Client\ClientInterface;
use Accord\Integration\Api\Client\ConfigInterface;
use Accord\Integration\Api\Request\RequestInterface;
use Accord\Integration\Api\CacheInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;

/**
 * @see https://wiki.itransition.com/display/AM2/Calculate+Cart
 */
class CalculateCart extends \Accord\Integration\Helper\Api
{
    const CACHE_PREFIX = 'calculateCart:';
    const CACHE_LIFETIME = 1;

    /** @var \Accord\Shipping\Helper\ShippingFromQuote */
    protected $shippingFromQuoteHelper;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $httpTracker;

    /**
     * @var \Accord\Integration\Api\Response\CalculateCartFactory
     */
    protected $responseFactory;

    /**
     * CalculateCart constructor.
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param CacheInterface $cache
     * @param ClientInterface $client
     * @param ConfigInterface $config
     * @param Registry $registry
     * @param \Magento\Framework\App\Request\Http $httpTracker
     * @param \Accord\Integration\Api\Response\CalculateCartFactory $responseFactory
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        CacheInterface $cache,
        ClientInterface $client,
        ConfigInterface $config,
        Registry $registry,
        \Magento\Framework\App\Request\Http $httpTracker,
        \Accord\Integration\Api\Response\CalculateCartFactory $responseFactory
    ) {
        parent::__construct($context, $objectManager, $cache, $client, $config, $registry);
        $this->httpTracker = $httpTracker;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @param array|null $data
     * @param RequestInterface $request
     * @return \Accord\Integration\Api\Response\CalculateCart | \Accord\Integration\Api\Response\ResponseInterface
     */
    public function calculateCart($data, RequestInterface $request = null)
    {
        if (!$request) {
            $request = $this->objectManager->get(\Accord\Integration\Api\Request\CalculateCart::class);
        }

        /** @var \Accord\Integration\Api\Response\CalculateCart $response */
        $response = $this->responseFactory->create();

        return (new \Accord\Integration\Api\Commands\CalculateCart($this->client, $request, $response))->execute($data);
    }

    /**
     * @param RequestInterface $request
     * @return \Accord\Integration\Api\Response\CalculateCart
     */
    public function calculateCartUseCache(RequestInterface $request)
    {
        /**
         * @see \Accord\Integration\Helper\CalculateCart::calculateCart
         */
        return $this->useCache('calculateCart', $request, self::CACHE_PREFIX, self::CACHE_LIFETIME);
    }

    /**
     * @param \Magento\Quote\Api\Data\CartInterface|\Magento\Quote\Model\Quote $quote
     * @param bool $reload
     * @return \Accord\Integration\Api\Response\CalculateCart|\Accord\Integration\Api\Response\ResponseInterface
     */
    public function calculateCartForSelectedCustomer(\Magento\Quote\Api\Data\CartInterface $quote, $reload = false)
    {
        /** @var \Accord\Integration\Api\Request\CalculateCartObject $request */
        $request = $this->objectManager->get(\Accord\Integration\Api\Request\CalculateCartObject::class);

        /** @var \Accord\Customer\Helper\Current\User $currentUser */
        $currentUser = $this->objectManager->create(\Accord\Customer\Helper\Current\User::class);
        $customerCode = $currentUser->getDataManager()->getCustomerCode();
        $pickupMethod = $currentUser->getDataManager()->getPickupMethod();
        $depot = $currentUser->getDataManager()->getDepot();

        $data = [
            $quote,
            $customerCode,
            null,
            null,
            null,
            $pickupMethod,
            $depot
        ];

        if ($this->checkAdditionToData()) {
            $data = [
                $quote,
                $customerCode,
                $this->getShippingFromQuoteHelper()->getDespatchDate($quote),
                $this->getShippingFromQuoteHelper()->getRoute($quote),
                $this->getShippingFromQuoteHelper()->getCarrierId($quote),
                $pickupMethod,
                $depot
            ];
        }

        $request->setData($data);

        return $this->getFromRequestCache('calculateCart', $request, [null, $request], $reload);
    }

    /**
     * @return bool
     */
    protected function checkAdditionToData()
    {
        $refererUrl = $this->httpTracker->getServer('HTTP_REFERER');
        $isAjax = $this->httpTracker->isAjax();

        if (substr($refererUrl, -1) !== '/') {
            $refererUrl .= '/';
        }

        $appropriateUrl = [
            $this->_urlBuilder->getUrl('checkout'),
            $this->_urlBuilder->getUrl('checkout/index'),
            $this->_urlBuilder->getUrl('checkout/index/index')
        ];

        return $isAjax && in_array($refererUrl, $appropriateUrl, true);
    }

    /**
     * @return \Accord\Shipping\Helper\ShippingFromQuote
     */
    protected function getShippingFromQuoteHelper()
    {
        if ($this->shippingFromQuoteHelper === null) {
            // TODO Avoid object manager usage
            $this->shippingFromQuoteHelper = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Accord\Shipping\Helper\ShippingFromQuote::class);
        }

        return $this->shippingFromQuoteHelper;
    }

}
