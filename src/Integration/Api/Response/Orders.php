<?php

namespace Accord\Integration\Api\Response;

/**
 * Class Orders
 * @package Accord\Integration\Api\Response
 * @property-read string $orderId
 * @property-read int $orderNo
 * @property-read string $orderReference
 * @property-read string $orderStatus
 * @property-read Order\Customer $customer
 * @property-read string $depot
 * @property-read string $channel
 * @property-read string $orderType
 * @property-read int $orderTypeCode
 * @property-read int $invoice
 * @property-read datetime $createdDate
 * @property-read datetime $deliveryDate
 * @property-read datetime $invoiceDate
 * @property-read int $weekNo
 * @property-read Order\OrderLine[] $orderLines
 * @property-read string $currency
 * @property-read number $subTotal
 * @property-read number $vatValue
 * @property-read number $orderSurchargeValue
 * @property-read number $orderDiscountValue
 * @property-read Order\Adjustments\Adjustment[] $adjustments
 * @property-read number $orderValue
 * @property-read int $totalLines
 * @property-read int $totalCases
 * @property-read int $totalSingles
 */
class Orders extends Response
{
    /**
     * @return void
     */
    protected function validate()
    {
        if (!is_string($this->orderId)) {
            throw $this->error('orderId is not specified');
        }

        if (!is_int($this->orderNo)) {
            throw $this->error('orderNo is not specified');
        }

        if (!is_string($this->orderReference)) {
            throw $this->error('orderReference is not specified');
        }

        if (!is_string($this->orderStatus)) {
            throw $this->error('orderStatus is not specified');
        }

        if (!is_string($this->depot)) {
            throw $this->error('depot is not specified');
        }

        if (!is_string($this->channel)) {
            throw $this->error('channel is not specified');
        }

        if (!is_string($this->orderType)) {
            throw $this->error('orderType is not specified');
        }

        if (!is_int($this->orderTypeCode)) {
            throw $this->error('orderTypeCode is not specified');
        }

        if (!is_int($this->invoice)) {
            throw $this->error('invoice is not specified');
        }

        if (!$this->createdDate) {
            throw $this->error('createdDate is not specified');
        }

        if (!$this->deliveryDate) {
            throw $this->error('deliveryDate is not specified');
        }

        if (!$this->invoiceDate) {
            throw $this->error('invoiceDate is not specified');
        }

        if (!is_int($this->weekNo)) {
            throw $this->error('totalSingles is not specified');
        }

        if (!is_array($this->orderLines)) {
            throw $this->error('orderLines is invalid');
        }

        if (!is_string($this->currency)) {
            throw $this->error('currency is invalid');
        }

        if (!is_string($this->currency)) {
            throw $this->error('currency is not specified');
        }

        if (!is_numeric($this->subTotal)) {
            throw $this->error('subTotal should be decimal');
        }

        if (!is_numeric($this->vatValue)) {
            throw $this->error('vatValue should be decimal');
        }

        if (!is_numeric($this->orderSurchargeValue)) {
            throw $this->error('orderSurchargeValue should be decimal');
        }

        if (!is_numeric($this->orderDiscountValue)) {
            throw $this->error('orderDiscountValue should be decimal');
        }

        if (!is_array($this->adjustments)) {
            throw $this->error('adjustments is invalid');
        }

        if (!is_numeric($this->orderValue)) {
            throw $this->error('orderValue should be decimal');
        }

        if (!is_int($this->totalLines)) {
            throw $this->error('totalLines is not specified');
        }

        if (!is_int($this->totalCases)) {
            throw $this->error('totalCases is not specified');
        }

        if (!is_int($this->totalSingles)) {
            throw $this->error('totalSingles is not specified');
        }

        $this->initCustomer();
        $this->initOrderLines();
        $this->initAdjustments();
    }

    /**
     * @return void
     */
    public function initCustomer()
    {
        /** @var array $customer */
        $customer = $this->customer;
        $this->customer = new Order\Customer($customer, $this);
    }

    /**
     * @return void
     */
    public function initOrderLines()
    {
        /** @var array $orderLines */
        $orderLines = $this->orderLines;
        $this->orderLines = [];

        foreach ($orderLines as $orderLine) {
            $this->orderLines[] = new Order\OrderLine($orderLine,  $this);
        }
    }

    /**
     * @return void
     */
    public function initAdjustments()
    {
        /** @var array $adjustments */
        $adjustments = $this->adjustments;
        $this->adjustments = [];

        foreach ($adjustments as $adjustment) {
            $this->adjustments[] = new Order\Adjustments\Adjustment($adjustment,  $this);
        }
    }

    /**
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return new \DateTime($this->createdDate);
    }

    /**
     * @return \DateTime
     */
    public function getDeliveryDate()
    {
        return new \DateTime($this->deliveryDate);
    }

    /**
     * @return \DateTime
     */
    public function getInvoiceDate()
    {
        return new \DateTime($this->invoiceDate);
    }
}
