<?php

namespace Accord\Integration\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Quote\Api\Data\AddressInterface;

/**
 * Class OverrideName
 */
class OverrideName implements OptionSourceInterface
{
    /**
     * @var string
     */
    public const SEPARATOR = '_';

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => AddressInterface::KEY_COMPANY,
                'label' => __('Company'),
            ],
            [
                'value' => AddressInterface::KEY_FIRSTNAME . self::SEPARATOR . AddressInterface::KEY_LASTNAME,
                'label' => __('Full Name'),
            ],
        ];
    }
}