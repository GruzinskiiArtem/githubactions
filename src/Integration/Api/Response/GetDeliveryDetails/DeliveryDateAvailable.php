<?php

namespace Accord\Integration\Api\Response\GetDeliveryDetails;

/**
 * @property-read \datetime $deliveryDate
 * @property-read DeliveryDateAvailable\ShippingOption[] $shippingOption (optional)
 */
class DeliveryDateAvailable extends \Accord\Integration\Api\Response\Item
{
    const ACCORD_DATE_FORMAT = 'Y-m-d\TH:i:s.uP';

    protected function initDeliveryDate()
    {
        $this->deliveryDate = \DateTime::createFromFormat(self::ACCORD_DATE_FORMAT, $this->deliveryDate);

        if (!$this->deliveryDate) {
            throw $this->error('deliveryDate is invalid');
        }
    }

    protected function initShippingOption()
    {
        $shippingOptions = [];

        /** @var array $option */
        foreach ($this->shippingOption as $option) {
            $shippingOptions[] = new DeliveryDateAvailable\ShippingOption($option);
        }

        if (!empty($shippingOptions)) {
            $freeOption = array_merge(
                $this->shippingOption[0],
                [
                    'carrierName' => DeliveryDateAvailable\ShippingOption::FREE_SHIPPING_LABEL,
                    'maxOrderValue' => DeliveryDateAvailable\ShippingOption::FREE_SHIPPING_MAX_ORDER_VALUE,
                    'deliveryCharge' => 0,
                ]
            );

            $shippingOptions[] = new DeliveryDateAvailable\ShippingOption($freeOption);
        }

        $this->shippingOption = $shippingOptions;
    }

    protected function validate()
    {
        if (!isset($this->deliveryDate)) {
            throw $this->error('deliveryDate is not specified');
        }
        $this->initDeliveryDate();

        if (!isset($this->shippingOption)) {
            $this->shippingOption = [];
        }
        if (!is_array($this->shippingOption)) {
            throw $this->error('shippingOption should be array');
        }
        $this->initShippingOption();
    }

    /**
     * @return number
     */
    public function getLowestDeliveryCharge()
    {
        $deliveryCharge = empty($this->shippingOption)
            ? 0
            : $this->shippingOption[0]->deliveryCharge;

        foreach ($this->shippingOption as $option) {
            if ($deliveryCharge > $option->deliveryCharge) {
                $deliveryCharge = $option->deliveryCharge;
            }
        }

        return $deliveryCharge;
    }
}
