<?php

namespace Accord\Integration\Api\Response;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

/**
 * Class Response
 *
 * @package Accord\Integration\Api\Response
 */
abstract class Response implements ResponseInterface, \Serializable
{
    /**
     * @var int
     */
    const DEFAULT_RESPONSE_CODE = 503;

    /**
     * @var PsrResponseInterface
     */
    private $response;

    /**
     * @var array
     */
    private $data;

    /**
     * @var array
     */
    protected $defaults = [];

    /**
     * Parser constructor.
     * @param PsrResponseInterface|null $response
     */
    public function __construct(PsrResponseInterface $response = null)
    {
        $this->setResponse($response);
    }

    /**
     * @param PsrResponseInterface|null $response
     *
     * @return $this
     */
    final public function setResponse(PsrResponseInterface $response = null)
    {
        $this->response = $response;

        $this->setData(json_decode($this->getBody(), true) ?: []);

        if ($this->response) {
            $this->validate();
        }

        return $this;
    }

    /**
     * @return void
     */
    abstract protected function validate();

    /**
     * @return PsrResponseInterface
     */
    final public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return string
     */
    final public function getBody()
    {
        return $this->getResponse() ? (string)$this->getResponse()->getBody() : '';
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return int
     */
    final public function getStatusCode()
    {
        return $this->getResponse() ? $this->getResponse()->getStatusCode() : self::DEFAULT_RESPONSE_CODE;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize($this->data);
    }

    /**
     * @param string $data
     */
    public function unserialize($data)
    {
        $this->setData((array)unserialize($data));
        $this->validate();
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        $data = $this->getData();

        if (isset($data[$name])) {
            return $data[$name];
        }

        throw new ResponseException('Property ' . $name . ' is not exists', 0, $this);
    }

    /**
     * @param int|string $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        $data = $this->getData();
        return isset($data[$key]);
    }

    /**
     * @param string $message
     * @param int $code
     *
     * @return ResponseException
     */
    protected function error(string $message, int $code = 0)
    {
        return new ResponseException($message, $code, $this);
    }

    /**
     * @param array $data
     */
    private function setData(array $data)
    {
        $this->data = array_merge($this->defaults, $data);
    }
}
