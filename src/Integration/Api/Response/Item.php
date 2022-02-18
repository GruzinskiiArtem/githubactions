<?php

namespace Accord\Integration\Api\Response;

/**
 * Class Item
 */
abstract class Item
{
    /**
     * @var array
     */
    protected $data;

    /**
     * Item constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->validate();
    }

    abstract protected function validate();

    protected function error($message, $code = 0)
    {
        return new ResponseException($message, $code);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
        throw $this->error('Property ' . $name . ' is not exists');
    }

    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

}