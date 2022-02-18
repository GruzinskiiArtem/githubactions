<?php

namespace Accord\Integration\Model\Cache;

abstract class Handler implements \Accord\Integration\Api\CacheInterface
{
    /**
     * @var Handler
     */
    private $nextHandler;

    /**
     * @param Handler|null $nextHandler
     */
    public function __construct(Handler $nextHandler = null)
    {
        $this->nextHandler = $nextHandler;
    }

    /**
     * @param string $key
     * @return mixed
     */
    final public function load($key)
    {
        $data = $this->processLoad($key);

        if ($data) {
            return $data;
        }

        if ($this->nextHandler) {
            $data = $this->nextHandler->load($key);
        }

        if ($data) {
            $this->processSave($key, $data);
        }

        return $data;
    }

    /**
     * @param string $key
     * @return mixed
     */
    abstract protected function processLoad($key);

    /**
     * @param string $key
     * @param mixed $data
     * @param null|int $lifeTime
     * @return void
     */
    final public function save($key, $data, $lifeTime = null)
    {
        $this->processSave($key, $data, $lifeTime);

        if ($this->nextHandler) {
            $this->nextHandler->save($key, $data, $lifeTime);
        }
    }

    /**
     * @param string $key
     * @param mixed $data
     * @param null|int $lifeTime
     * @return void
     */
    abstract protected function processSave($key, $data, $lifeTime = null);

    /**
     * @param string $key
     * @return void
     */
    final public function remove($key)
    {
        $this->processRemove($key);

        if ($this->nextHandler) {
            $this->nextHandler->remove($key);
        }
    }

    /**
     * @param string $key
     * @return void
     */
    abstract protected function processRemove($key);

    /**
     * @return void
     */
    final public function clean()
    {
        $this->processClean();

        if ($this->nextHandler) {
            $this->nextHandler->clean();
        }
    }

    /**
     * @return void
     */
    abstract protected function processClean();
}
