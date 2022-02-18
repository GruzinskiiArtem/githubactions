<?php
namespace Accord\Integration\Api\Response\Invoices;

/**
 * @property-read datetime $invoiceDate
 * @property-read int $weekNo
 * @property-read int $invoice
 * @property-read datetime $customerRef
 * @property-read float $total
 * @property-read string $orderType
 * @property-read string $depot
 * @property-read datetime $deliveryDate
 * @property-read int $orderNo Optional
 * @property-read string $orderId Optional
 */
class Item extends \Accord\Integration\Api\Response\Item
{

    public function getInvoiceDate()
    {
        return new \DateTime($this->invoiceDate);
    }

    public function getDeliveryDate()
    {
        return new \DateTime($this->deliveryDate);
    }

    protected function validate()
    {
        if (!$this->invoiceDate) {
            throw $this->error('invoiceDate is not specified');
        }
        
        if (!is_int($this->weekNo)) {
            throw $this->error('weekNo must be integer');
        }
        
        if (!is_int($this->invoice)) {
            throw $this->error('invoice must be integer');
        }
        
        if (!is_string($this->customerRef)) {
            throw $this->error('customerRef is not specified');
        }
        
        if (!is_numeric($this->total)) {
            throw $this->error('must be decimal');
        }
        
        if (!$this->orderType) {
            throw $this->error('orderType is not specified');
        }
        
        if (!$this->depot) {
            throw $this->error('depot is not specified');
        }
        
        if (!$this->deliveryDate) {
            throw $this->error('deliveryDate is not specified');
        }
    }
}