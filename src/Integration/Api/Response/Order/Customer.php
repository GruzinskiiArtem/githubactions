<?php

namespace Accord\Integration\Api\Response\Order;

/**
 * @property-read int $code
 * @property-read int $name
 * @property-read int $user
 */
class Customer extends \Accord\Integration\Api\Response\Item
{
    protected function validate()
    {
        if (!is_string($this->code)) {
            throw $this->error('code is not specified');
        }

        if (!is_string($this->name)) {
            throw $this->error('name is not specified');
        }

        if (!is_string($this->user)) {
            throw $this->error('user is not specified');
        }
    }
}
