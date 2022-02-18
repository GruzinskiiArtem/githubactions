<?php

declare(strict_types=1);

namespace Accord\Integration\Api\Response\GetDepots;

use Accord\General\Helper\Config\Catalog\Product as ProductHelper;
use Accord\Integration\Api\Response\Item;

/**
 * @property-read string $depot
 * @property-read string $depotName
 * @property-read string $address1
 * @property-read string $address2
 * @property-read string $address3
 * @property-read string $address4
 * @property-read string $postcode
 * @property-read string $customer //deprecated
 * @property-read string $guestPickupMethod //deprecated
 * @property-read string $b2bGuestCustomer
 * @property-read string $b2bGuestPickupMethod
 * @property-read string $b2cGuestCustomer
 * @property-read string $b2cGuestPickupMethod
 * @property-read string $registeredPickupMethod
 */
class Depot extends Item
{
    /**
     * @var string
     * @deprecared
     * @comment Kept for backwards compatibility
     */
    public const PICKUP_METHOD_GUEST = 'guestPickupMethod';

    /**
     * @var string
     */
    public const PICKUP_METHOD_VAT_GUEST = 'b2cGuestPickupMethod';

    /**
     * @var string
     */
    public const PICKUP_METHOD_NO_VAT_GUEST = 'b2bGuestPickupMethod';

    /**
     * @var string
     */
    public const PICKUP_METHOD_REGISTERED = 'registeredPickupMethod';

    /**
     * @var bool|null
     */
    private $newApi = null;

    /**
     * @var ProductHelper
     */
    private $productHelper;

    public function __construct(ProductHelper $productHelper, array $data)
    {
        parent::__construct($data);
        $this->productHelper = $productHelper;
    }

    /**
     * @return void
     */
    protected function validate()
    {
        if (!is_string($this->depot)) {
            throw $this->error('depot should be string');
        }

        if (!is_string($this->depotName)) {
            throw $this->error('depotName should be string');
        }

        if (!is_string($this->address1)) {
            throw $this->error('address1 should be string');
        }

        if (!is_string($this->address2)) {
            throw $this->error('address2 should be string');
        }

        if (!is_string($this->address3)) {
            throw $this->error('address3 should be string');
        }

        if (!is_string($this->address4)) {
            throw $this->error('address4 should be string');
        }

        if (!is_string($this->postcode)) {
            throw $this->error('postcode should be string');
        }

        if (!is_string($this->registeredPickupMethod)) {
            throw $this->error('collection should be boolean');
        }
    }

    /**
     * Retrieve full depot address
     *
     * @return string
     */
    public function getFullAddress(): string
    {
        $address = [$this->address1, $this->address2, $this->address3, $this->address4, $this->postcode];

        array_walk($address, 'trim');
        $address = array_filter($address);

        return implode(' ', $address);
    }

    /**
     * @return bool|null
     */
    public function isNewApi(): ?bool
    {
        if (is_null($this->newApi)) {
            $this->newApi = !isset($this->data[self::PICKUP_METHOD_GUEST])
                && (
                    isset($this->data[self::PICKUP_METHOD_NO_VAT_GUEST])
                    || isset($this->data[self::PICKUP_METHOD_VAT_GUEST])
                );
        }

        return $this->newApi;
    }

    /**
     * @return bool
     */
    public function hasGuestVatInclusive(): bool
    {
        return isset($this->data[self::PICKUP_METHOD_VAT_GUEST]);
    }

    /**
     * @return string
     */
    public function getCustomerCode(): string
    {
        if ($this->isNewApi()) {
            $isIncVat = $this->productHelper->isCatalogIncludeVat();
            $code = $isIncVat
                ? $this->b2cGuestCustomer
                : $this->b2bGuestCustomer;

            return $code ?: ($isIncVat
                ? $this->b2bGuestCustomer
                : $this->b2cGuestCustomer);
        }

        return $this->customer;
    }

    /**
     * @return string
     */
    public function getGuestPickupMethod(): string
    {
        if ($this->isNewApi()) {
            return $this->productHelper->isCatalogIncludeVat()
                ? $this->b2cGuestPickupMethod
                : $this->b2bGuestPickupMethod;
        }

        return $this->guestPickupMethod;
    }

    /**
     * @return array
     */
    public function getDefaultCodes(): array
    {
        return $this->isNewApi()
            ? array_filter([$this->b2bGuestCustomer, $this->b2cGuestCustomer])
            : [$this->customer];
    }
}
