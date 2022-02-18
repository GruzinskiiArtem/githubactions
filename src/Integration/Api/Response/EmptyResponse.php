<?php

namespace Accord\Integration\Api\Response;

/**
 * Class EmptyResponse
 *
 * @package Accord\Integration\Api\Response
 */
final class EmptyResponse extends Response
{
    /**
     * @return void
     */
    protected function validate()
    {
        if ($this->getBody()) {
            throw $this->error('Body must be empty');
        }
    }
}
