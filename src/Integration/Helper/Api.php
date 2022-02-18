<?php
namespace Accord\Integration\Helper;

use Accord\Integration\Api\Client\ClientInterface;
use Accord\Integration\Api\Client\ConfigInterface;
use Accord\Integration\Api\Request\RequestInterface;
use Accord\Integration\Api\CacheInterface;
use Accord\Integration\Api\Response\ResponseInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;

abstract class Api extends AbstractHelper
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var array
     */
    private $requestCache = [];

    /**
     * Api constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param CacheInterface $cache
     * @param ClientInterface $client
     * @param ConfigInterface $config
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        CacheInterface $cache,
        ClientInterface $client,
        ConfigInterface $config,
        Registry $registry
    ) {
        parent::__construct($context);
        $this->client = clone $client;
        $this->client->init($config);
        $this->objectManager = $objectManager;
        $this->cache = $cache;
        $this->registry = $registry;
    }


    /**
     * @param string $method (array|null $data, RequestInterface $request)
     * @param string $cachePrefix
     * @param RequestInterface $request
     * @param int $lifeTime
     * @param bool $updateCache
     * @return \Accord\Integration\Api\Response\ResponseInterface
     */
    protected function useCache(
        $method,
        RequestInterface $request,
        $cachePrefix = null,
        $lifeTime = 60,
        $updateCache = false
    ) {
        $key = $this->key($request->getData(), $cachePrefix ?? $method);

        if ($updateCache) {
            $this->cache->remove($key);
        } else {
            $cached = $this->cache->load($key);

            if ($cached) {
                return $cached;
            }
        }

        $data = $this->$method(null, $request);

        if ($data) {
            $this->cache->save($key, $data, $lifeTime);
        }

        return $data;
    }

    /**
     * @param string $method
     * @param array $data
     *
     * @return ResponseInterface
     */
    protected function useRegistry(string $method, array $data): ResponseInterface
    {
        $key = $this->key($data, $method);

        if ($this->registry->registry($key)) {
            return $this->registry->registry($key);
        }

        $data = $this->$method($data);
        $this->registry->register($key, $data);

        return $data;
    }


    /**
     * cache data on the level of session request
     *
     * @param $method
     * @param RequestInterface $request
     * @param array $args
     * @param bool $reload
     * @return \Accord\Integration\Api\Response\ResponseInterface
     */
    protected function getFromRequestCache(
        $method,
        \Accord\Integration\Api\Request\RequestInterface $request,
        $args = [],
        $reload = false
    ) {
        $key = $this->key($request->getData(), $method);

        if ($reload || !isset($this->requestCache[$key])) {
            $this->requestCache[$key] = call_user_func_array([$this, $method], $args);
        }

        return $this->requestCache[$key];
    }

    /**
     * @param array $data
     * @param string $prefix
     * @return string
     */
    private function key(array $data, string $prefix = ''): string
    {
        $key = sprintf(
            '%s_%s_%s',
            self::class,
            $prefix,
            md5(json_encode($data))
        );

        return strtolower(str_replace(['/', '\\'], '_', $key));
    }
}
