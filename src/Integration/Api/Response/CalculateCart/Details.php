<?php
namespace Accord\Integration\Api\Response\CalculateCart;

use Accord\Integration\Api\Response\Item;

/**
 * @property-read string(70) $detailText (optional)
 * @property-read number $detailValue (optional)
 */
class Details extends Item
{

    protected function validate()
    {
        if (!isset($this->detailText)) {
            $this->detailText = '';
        }
        if (!isset($this->detailValue)) {
            $this->detailValue = 0;
        }

        if (!$this->detailText) {
            throw $this->error('detailText is not specified');
        }
        if (!is_numeric($this->detailValue)) {
            throw $this->error('detailValue is invalid');
        }
    }

}