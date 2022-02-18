<?php

namespace Accord\Integration\Api\Response\GetDeliveryDetails;

/**
 * @property-read number $maxOrderValue
 * @property-read number $deliveryCharge
 */
class SimpleShippingOption extends \Accord\Integration\Api\Response\Item
{
    protected function validate()
    {
        if (!is_numeric($this->maxOrderValue)) {
            throw $this->error('maxOrderValue should be decimal');
        }
        if (!is_numeric($this->deliveryCharge)) {
            throw $this->error('deliveryCharge should be decimal');
        }
    }
}
