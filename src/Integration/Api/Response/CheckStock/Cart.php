<?php
namespace Accord\Integration\Api\Response\CheckStock;

use Accord\Integration\Api\Response\Item;

/**
 * @property-read string $id (optional)
 * @property-read string $productSku
 * @property-read number $actualStockLevel
 * @property-read array $substituteProductSkus (optional)
 */
class Cart extends Item
{

    protected function validate()
    {
        if (!isset($this->id)) {
            $this->id = null;
        }

        if (!($this->productSku)) {
            throw $this->error('productSku is empty');
        }

        if (!is_numeric($this->actualStockLevel)) {
            throw $this->error('actualStockLevel is invalid');
        }

        if (!isset($this->substituteProductSkus)) {
            $this->substituteProductSkus = [];
        }
        if (!is_array($this->substituteProductSkus)) {
            throw $this->error('substituteProductSkus must be array');
        }
    }

}