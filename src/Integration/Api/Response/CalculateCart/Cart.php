<?php
namespace Accord\Integration\Api\Response\CalculateCart;

use Accord\Integration\Api\Response\Item;

/**
 * @property-read string $id (optional)
 * @property-read number $price
 * @property-read number $lineValue
 */
class Cart extends Item
{

    protected function validate()
    {
        if (!isset($this->id)) {
            $this->id = null;
        }
        if (!is_numeric($this->price)) {
            throw $this->error('price is invalid');
        }

        if (!is_numeric($this->lineValue)) {
            throw $this->error('lineValue is invalid');
        }
    }

}