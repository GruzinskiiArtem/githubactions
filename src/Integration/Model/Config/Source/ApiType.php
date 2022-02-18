<?php

declare(strict_types=1);

namespace Accord\Integration\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ApiType implements OptionSourceInterface
{
    /**
     * @var string
     */
    public const TYPE_ACCORD = 'accord';

    /**
     * @var string
     */
    public const TYPE_SAGE = 'sage';

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        return [
            ['label' => __('Accord'), 'value' => self::TYPE_ACCORD],
            ['label' => __('Sage'), 'value' => self::TYPE_SAGE],
        ];
    }
}
