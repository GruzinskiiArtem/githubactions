<?php

namespace Accord\Integration\Api\Response\GetUser;

use Accord\Integration\Api\Response\Item;
use Accord\Integration\Helper\User;

/**
 * @property-read string $customerCode
 * @property-read string $customerName
 * @property-read string $shortName
 * @property-read string $currencyCode
 * @property-read string $pickupMethod
 * @property-read array $validOffers
 * @property-read array $favProductSkus
 * @property-read array $paymentMethods
 * @property-read array $deliveryAddress
 */
class AssociatedCustomer extends Item
{
    public $isExists = true;

    protected function validate()
    {
        if (!$this->customerCode) {
            throw $this->error('customerCode is not specified');
        }
        if (!$this->customerName) {
            throw $this->error('customerName is not specified');
        }
        if (!$this->shortName) {
            throw $this->error('shortName is not specified');
        }
        if (!$this->currencyCode) {
            throw $this->error('currencyCode is not specified');
        }

        if (!isset($this->validOffers) || !is_array($this->validOffers)) {
            $this->validOffers = [];
        }

        foreach ($this->validOffers as $validOffer) {
            if (!isset($validOffer['offerCode'])) {
                throw $this->error('Property offerCode is not exists');
            }
            if (!isset($validOffer['offerType'])) {
                throw $this->error('Property offerType is not exists');
            }
        }

        if (!is_array($this->paymentMethods)) {
            throw $this->error('paymentMethods isn\'t valid');
        }

        if (!is_string($this->pickupMethod)) {
            throw $this->error('pickupMethod isn\'t valid');
        }

        if (!isset($this->favProductSkus)) {
            $this->favProductSkus = [];
        }
    }

    /**
     * @return array
     */
    public function getOfferCodes(): array
    {
        return array_column($this->validOffers, 'offerCode');
    }

    /**
     * @return bool
     */
    public function canOrder(): bool
    {
        return !(isset($this->creditStatus) && $this->creditStatus === User::CREDIT_STATUS_ON_STOP);
    }
}
