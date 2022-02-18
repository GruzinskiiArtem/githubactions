<?php

namespace Accord\Integration\Api;

/**
 * Interface CacheInterface
 *
 * @package Accord\Integration\Api
 */
interface CacheInterface
{
    /**
     * Load data from cache by id
     *
     * @param string $identifier
     * @return mixed
     */
    public function load($key);

    /**
     * @param string $key
     * @param mixed $data
     * @param null|int $lifeTime
     * @return void
     */
    public function save($key, $data, $lifeTime = null);

    /**
     * @param string $key
     * @return void
     */
    public function remove($key);

    /**
     * @return void
     */
    public function clean();
}
