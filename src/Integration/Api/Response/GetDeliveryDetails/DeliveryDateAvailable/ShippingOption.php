<?php

namespace Accord\Integration\Api\Response\GetDeliveryDetails\DeliveryDateAvailable;

/**
 * @property-read int $carrierID
 * @property-read string $carrierName
 * @property-read \datetime $despatchDate
 * @property-read string $route (optional)
 * @property-read number $maxOrderValue
 * @property-read number $deliveryCharge
 */
class ShippingOption extends \Accord\Integration\Api\Response\Item
{
    const ACCORD_DATE_FORMAT = 'Y-m-d\TH:i:s.uP';

    const FREE_SHIPPING_LABEL = 'Free';

    const FREE_SHIPPING_MAX_ORDER_VALUE = PHP_INT_MAX;

    protected function initDespatchDate()
    {
        $this->despatchDate = \DateTime::createFromFormat(self::ACCORD_DATE_FORMAT, $this->despatchDate);

        if (!$this->despatchDate) {
            throw $this->error('despatchDate is invalid');
        }
    }

    protected function validate()
    {
        if (!is_int($this->carrierID)) {
            throw $this->error('carrierID should be int');
        }

        if (!is_string($this->carrierName)) {
            throw $this->error('carrierName should be string');
        }

        if (!isset($this->despatchDate)) {
            throw $this->error('despatchDate is not specified');
        }
        $this->initDespatchDate();

        if (!isset($this->route)) {
            $this->route = '';
        }
        if (!is_string($this->route)) {
            throw $this->error('route should be string');
        }

        if (!is_numeric($this->maxOrderValue)) {
            throw $this->error('maxOrderValue should be decimal');
        }

        if (!is_numeric($this->deliveryCharge)) {
            throw $this->error('deliveryCharge should be decimal');
        }
    }
}
