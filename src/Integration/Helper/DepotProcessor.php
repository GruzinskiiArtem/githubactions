<?php

namespace Accord\Integration\Helper;

use Accord\General\Helper\Config\Catalog\Product as ProductHelper;
use Accord\Integration\Api\CacheInterface;
use Accord\Integration\Api\Client\ClientInterface;
use Accord\Integration\Api\Client\ConfigInterface;
use Accord\Integration\Api\Request\RequestInterface;
use Accord\Integration\Api\Response\GetDepots;
use Accord\Integration\Api\Response\GetDepots\Depot;
use Accord\Integration\Api\Response\ResponseInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;

/**
 * Class DepotProcessor
 */
class DepotProcessor extends Api
{
    /**
     * @var string
     */
    public const CACHE_PREFIX = 'depotProcessor:';

    /**
     * @var int
     */
    public const CACHE_LIFETIME = 3600;

    /**
     * @var ProductHelper
     */
    private $productHelper;

    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        CacheInterface $cache,
        ClientInterface $client,
        ConfigInterface $config,
        Registry $registry,
        ProductHelper $productHelper
    ) {
        parent::__construct($context, $objectManager, $cache, $client, $config, $registry);
        $this->productHelper = $productHelper;
    }

    /**
     * @param RequestInterface|null $request
     *
     * @return GetDepots|ResponseInterface
     */
    public function getDepots(RequestInterface $request = null): ResponseInterface
    {
        if (!$request) {
            $request = $this->objectManager->get(\Accord\Integration\Api\Request\GetDepots::class);
        }

        $response = $this->objectManager->get(GetDepots::class);
        $command = new \Accord\Integration\Api\Commands\GetDepots($this->client, $request, $response);

        return $command->execute();
    }

    /**
     * @param bool $updateCache
     *
     * @return GetDepots
     */
    public function getDepotsList(bool $updateCache = false): GetDepots
    {
        $request = $this->objectManager->get(\Accord\Integration\Api\Request\GetDepots::class);

        return $this->getDepotsUseCache($request, $updateCache);
    }

    /**
     * @param RequestInterface $request
     * @param bool $updateCache
     *
     * @return GetDepots|ResponseInterface
     */
    public function getDepotsUseCache(RequestInterface $request, bool $updateCache = false): ResponseInterface
    {
        return $this->useCache('getDepots', $request, self::CACHE_PREFIX, self::CACHE_LIFETIME, $updateCache);
    }

    /**
     * @param bool $isLoggedIn
     * @return string
     */
    public function getPickupMethodPropertyByAuthStatus(bool $isLoggedIn = false): string
    {
        return $isLoggedIn
            ? Depot::PICKUP_METHOD_REGISTERED
            : $this->getGuestPickUpMethodKey();
    }

    /**
     * @return string
     */
    private function getGuestPickUpMethodKey(): string
    {
        $defaultDepot = $this->getDepotsList()->getDepot();

        if ($defaultDepot->isNewApi()) {
            return $this->productHelper->isCatalogIncludeVat()
                ? Depot::PICKUP_METHOD_VAT_GUEST
                : Depot::PICKUP_METHOD_NO_VAT_GUEST;
        }

        return Depot::PICKUP_METHOD_GUEST;
    }
}
