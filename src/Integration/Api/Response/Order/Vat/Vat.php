<?php

namespace Accord\Integration\Api\Response\Order\Vat;

/**
 * @property-read string $code
 * @property-read number $rate
 * @property-read number $value
 */
class Vat extends \Accord\Integration\Api\Response\Item
{
    protected function validate()
    {
        if (!is_string($this->code)) {
            throw $this->error('code is not specified');
        }

        if (!is_numeric($this->rate)) {
            throw $this->error('rate is not specified');
        }

        if (!is_numeric($this->value)) {
            throw $this->error('value should be decimal');
        }
    }
}
