<?php

namespace Accord\Integration\Api\Response\Order;

/**
 * @property-read int $orderLineNumber
 * @property-read string $productSku
 * @property-read string $originalProductSku
 * @property-read string $ean
 * @property-read string $tuc
 * @property-read string $productDescription
 * @property-read ProductGroup\ProductGroup|array $productGroup
 * @property-read ProductSubGroup\ProductSubGroup|array $productSubGroup
 * @property-read string $lineMsg
 * @property-read int $quantity
 * @property-read string $quantityType
 * @property-read string $productUnits
 * @property-read number $weight
 * @property-read string $lineType
 * @property-read string $specialOfferCode
 * @property-read number $price
 * @property-read int $originalInvoice
 * @property-read Vat\Vat|array $vat
 * @property-read number $lineValue
 */
class OrderLine extends \Accord\Integration\Api\Response\Item
{
    protected function validate()
    {
        if (!is_int($this->orderLineNumber)) {
            throw $this->error('orderLineNumber is not specified');
        }

        if (!is_string($this->productSku)) {
            throw $this->error('productSku is not specified');
        }

        if (!is_string($this->originalProductSku)) {
            throw $this->error('originalProductSku is not specified');
        }

        if (!is_string($this->ean)) {
            throw $this->error('ean is not specified');
        }

        if (!is_string($this->tuc)) {
            throw $this->error('tuc is not specified');
        }

        if (!$this->productDescription) {
            throw $this->error('productDescription is not specified');
        }

        if (!is_string($this->lineMsg)) {
            throw $this->error('lineMsg is not specified');
        }

        if (!is_int($this->quantity)) {
            throw $this->error('quantity is not specified');
        }

        if (!is_string($this->quantityType)) {
            throw $this->error('quantityType is not specified');
        }

        if (!is_string($this->productUnits)) {
            throw $this->error('productUnits is not specified');
        }

        if (!is_numeric($this->weight)) {
            throw $this->error('weight should be decimal');
        }

        if (!is_string($this->lineType)) {
            throw $this->error('lineType is not specified');
        }

        if (!is_string($this->specialOfferCode)) {
            throw $this->error('specialOfferCode is not specified');
        }

        if (!is_numeric($this->price)) {
            throw $this->error('specialOfferCode should be decimal');
        }

        if (!is_int($this->originalInvoice)) {
            throw $this->error('originalInvoice is not specified');
        }

        $this->initOrderLines();
        $this->initProductSubGroup();
        $this->initVat();
    }

    public function initOrderLines()
    {
        $this->productGroup = new ProductGroup\ProductGroup($this->productGroup);
    }

    public function initProductSubGroup()
    {
        $this->productSubGroup = new ProductSubGroup\ProductSubGroup($this->productSubGroup);
    }

    public function initVat()
    {
        $this->vat = new Vat\Vat($this->vat);
    }
}
