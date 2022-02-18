<?php
namespace Accord\Integration\Api\Request;

abstract class Request implements RequestInterface
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Request constructor.
     * @param array|null $data
     */
    public function __construct($data = null)
    {
        if ($data) {
            $this->setData($data);
        }
    }

    /**
     * @param array $data
     * @return $this
     */
    final public function setData($data)
    {
        $this->data = $this->convert($data);

        return $this;
    }

    /**
     * @param mixed $data
     * @return array
     */
    abstract protected function convert($data);

    /**
     * @return array
     */
    final public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return [];
    }

}
