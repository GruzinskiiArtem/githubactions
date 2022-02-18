<?php

declare(strict_types=1);

namespace Accord\Integration\Api\Response;

use DateTime;

/**
 * @property array $deliveryAddress
 * @property DateTime $defaultDeliveryDate
 * @property GetDeliveryDetails\SimpleShippingOption|null $simpleShippingOption (optional)
 * @property GetDeliveryDetails\DeliveryDateAvailable[] $deliveryDatesAvailable
 */
class GetDeliveryDetails extends Response
{
    const API_DATE_FORMAT = 'Y-m-d\TH:i:s.uP';

    /**
     * @var DateTime
     */
    protected $_defaultDeliveryDate;

    /**
     * @return DateTime
     */
    public function getDefaultDeliveryDate(): DateTime
    {
        return $this->_defaultDeliveryDate;
    }

    /**
     * @return void
     */
    protected function initDefaultDeliveryDate()
    {
        $this->_defaultDeliveryDate = DateTime::createFromFormat(self::API_DATE_FORMAT, $this->defaultDeliveryDate);

        if (!$this->_defaultDeliveryDate) {
            throw $this->error('defaultDeliveryDate is invalid');
        }
    }

    /**
     * @return void
     */
    protected function initSimpleShippingOption(): void
    {
        /** @var array $simpleShippingOptionData */
        $simpleShippingOptionData = $this->simpleShippingOption;
        $this->simpleShippingOption = new GetDeliveryDetails\SimpleShippingOption($simpleShippingOptionData);
    }

    /**
     * @return void
     */
    public function initDeliveryDatesAvailable(): void
    {
        $deliveryDatesAvailable = [];

        /** @var array $deliveryDateAvailable */
        foreach ($this->deliveryDatesAvailable as $deliveryDateAvailable) {
            if (is_array($deliveryDateAvailable)) {
                $deliveryDatesAvailable[] = new GetDeliveryDetails\DeliveryDateAvailable($deliveryDateAvailable);
            }
        }

        $this->deliveryDatesAvailable = $deliveryDatesAvailable;
    }

    /**
     * @inheritDoc
     */
    protected function validate(): void
    {
        if (!$this->deliveryAddress) {
            throw $this->error('deliveryAddress is not specified');
        }
        if (!is_array($this->deliveryAddress)) {
            throw $this->error('deliveryAddress is invalid');
        }

        if (isset($this->simpleShippingOption) && is_array($this->simpleShippingOption)) {
            $this->initSimpleShippingOption();
        } else {
            $this->simpleShippingOption = null;
        }

        if (!$this->defaultDeliveryDate) {
            throw $this->error('defaultDeliveryDate is not specified');
        }
        $this->initDefaultDeliveryDate();

        if (!is_array($this->deliveryDatesAvailable)) {
            throw $this->error('deliveryDatesAvailable is invalid');
        }
        $this->initDeliveryDatesAvailable();
    }
}
