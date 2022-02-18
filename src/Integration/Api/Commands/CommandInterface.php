<?php

namespace Accord\Integration\Api\Commands;

use Accord\Integration\Api\Client\ClientInterface;
use Accord\Integration\Api\Request\RequestInterface;
use Accord\Integration\Api\Response\ResponseInterface;

interface CommandInterface
{
    /**
     * @param mixed $data
     * @return ResponseInterface
     */
    public function execute($data = null);

    /**
     * @return string
     */
    public function getName();

    /**
     * @return ClientInterface
     */
    public function getClient();

    /**
     * @return RequestInterface
     */
    public function getRequest();

    /**
     * @return ResponseInterface
     */
    public function getResponse();

    /**
     * @return string
     */
    public function getUrl(): string;
}
