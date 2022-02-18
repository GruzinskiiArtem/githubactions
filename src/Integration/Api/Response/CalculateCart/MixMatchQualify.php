<?php
namespace Accord\Integration\Api\Response\CalculateCart;

use Accord\Integration\Api\Response\Item;

/**
 * @property-read string $mixMatchCode
 * @property-read integer $qualified
 * @property-read number $rebate
 * @property-read integer $freeQuantity (optional)
 * @property-read string $freeQuantityType (optional)
 */
class MixMatchQualify extends Item
{

    protected function validate()
    {
        if (!$this->mixMatchCode) {
            throw $this->error('mixMatchCode is not specified');
        }
        if (!is_int($this->qualified)) {
            throw $this->error('qualified is invalid');
        }
        if (!is_numeric($this->rebate)) {
            throw $this->error('rebate is invalid');
        }

        if (!isset($this->freeQuantity)) {
            $this->freeQuantity = 0;
        }
        if (!is_int($this->freeQuantity)) {
            throw $this->error('freeQuantity is invalid');
        }

        if (!isset($this->freeQuantityType)) {
            $this->freeQuantityType = "";
        }
        if ($this->freeQuantityType) {
            if ($this->freeQuantityType != "cases" && $this->freeQuantityType != "singles") {
                throw $this->error('freeQuantityType is invalid');
            }
        }
    }

}