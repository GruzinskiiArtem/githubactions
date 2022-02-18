<?php

namespace Accord\Integration\Api\Response\Order\ProductGroup;

/**
 * @property-read string $code
 * @property-read string $description
 */
class ProductGroup extends \Accord\Integration\Api\Response\Item
{
    protected function validate()
    {
        if (!is_string($this->code)) {
            throw $this->error('code is not specified');
        }

        if (!is_string($this->description)) {
            throw $this->error('description is not specified');
        }
    }
}
