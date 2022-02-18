<?php

namespace Accord\Integration\Api\Response;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

interface ResponseInterface
{
    /**
     * @param PsrResponseInterface $response
     * @return ResponseInterface
     */
    public function setResponse(PsrResponseInterface $response);

    /**
     * @return PsrResponseInterface
     */
    public function getResponse();

    /**
     * @return \Psr\Http\Message\StreamInterface
     */
    public function getBody();

    /**
     * @return array
     */
    public function getData();

    /**
     * @return int
     */
    public function getStatusCode();
}
