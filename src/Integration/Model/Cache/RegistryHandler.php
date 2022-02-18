<?php

namespace Accord\Integration\Model\Cache;

class RegistryHandler extends Handler
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @param string $key
     * @return mixed
     */
    protected function processLoad($key)
    {
        return $this->data[$key] ?? null;
    }

    /**
     * @param string $key
     * @param mixed $data
     * @param null $lifeTime
     * @return void
     */
    protected function processSave($key, $data, $lifeTime = null)
    {
        $this->data[$key] = $data;
    }

    /**
     * @param string $key
     * @return void
     */
    protected function processRemove($key)
    {
        unset($this->data[$key]);
    }

    /**
     * @return void
     */
    protected function processClean()
    {
        $this->data = [];
    }
}
