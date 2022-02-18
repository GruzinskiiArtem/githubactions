<?php

namespace Accord\Integration\Model\Cache;

class CacheHandler extends Handler
{
    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    private $cache;

    /**
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param Handler|null $nextHandler
     */
    public function __construct(
        \Magento\Framework\App\CacheInterface $cache,
        Handler $nextHandler = null
    ) {
        parent::__construct($nextHandler);
        $this->cache = $cache;
    }

    /**
     * @param string $key
     * @return mixed
     */
    protected function processLoad($key)
    {
        $data = $this->cache->load($key);
        return $data ? unserialize($data) : null;
    }

    /**
     * @param string $key
     * @param mixed $data
     * @param null $lifeTime
     * @return void
     */
    protected function processSave($key, $data, $lifeTime = null)
    {
        $this->cache->save(serialize($data), $key, [], $lifeTime);
    }

    /**
     * @param string $key
     * @return void
     */
    protected function processRemove($key)
    {
        $this->cache->remove($key);
    }

    /**
     * @return void
     */
    protected function processClean()
    {
        $this->cache->clean();
    }
}
