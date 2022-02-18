<?php

declare(strict_types=1);

namespace Accord\Integration\Api\Client;

use GuzzleHttp\HandlerStack;

interface ConfigInterface
{
    /**
     * @return string|null
     */
    public function getRestApiEndpoint(): ?string;

    /**
     * @return string|null
     */
    public function getRestApiUsername(): ?string;

    /**
     * @return string|null
     */
    public function getRestApiPassword(): ?string;

    /**
     * @return string|null
     */
    public function getApiType(): ?string;

    /**
     * @return HandlerStack|null
     */
    public function getHandler(): ?HandlerStack;
}
