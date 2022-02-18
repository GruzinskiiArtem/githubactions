<?php

namespace Accord\Integration\Model;

use Accord\General\Helper\Config\Catalog\Product;
use Accord\Integration\Api\Response\Orders;

/**
 * Class OrderFormatter
 */
class OrderFormatter extends OrderFormatterAbstract
{
    /**
     * @var string[]
     */
    public static $orderTypeMask = [
        10 => 'Telesales',
        11 => 'Field Sales',
        12 => 'Daybreak Amb',
        13 => 'Debenhams',
        14 => 'Daybreak Ch',
        20 => 'Promotions',
        21 => 'Online C&C',
        22 => 'Online Del'
    ];

    /**
     * @var Product
     */
    private $productHelper;

    /**
     * OrderFormatter constructor.
     *
     * @param Product $productHelper
     */
    public function __construct(Product $productHelper)
    {
        $this->productHelper = $productHelper;
    }

    /**
     * @param Orders $order
     *
     * @return array|string
     */
    public function toCsv(Orders $order): string
    {
        $this->order = $order;

        $data = [
            self::CSV_COLUMN_NAMES,
        ];

        foreach ($this->order->orderLines as $orderLine) {
            $data[] = [
                $this->order->invoice,
                $this->order->invoiceDate,
                $this->order->orderNo,
                $this->order->createdDate,
                $this->order->orderReference,
                $this->order->weekNo,
                $this->order->customer->code,
                $this->order->customer->name,
                $this->getFormattedOrderValue($this->order->orderValue),
                $this->order->currency,
                $this->order->orderValue,
                $this->order->vatValue,
                $this->getMaskByOrderTypeCode($this->order->orderTypeCode, self::$orderTypeMask),
                $this->order->channel,
                $this->order->depot,
                $this->order->deliveryDate,
                $orderLine->orderLineNumber,
                $orderLine->productSku,
                $orderLine->ean,
                $orderLine->tuc,
                $orderLine->productDescription,
                $orderLine->productUnits,
                $orderLine->productGroup->code,
                $orderLine->productGroup->description,
                $orderLine->productSubGroup->code,
                $orderLine->productSubGroup->description,
                $orderLine->weight > 0 ? self::YES : self::NO,
                $orderLine->quantity,
                $orderLine->quantityType,
                $orderLine->price,
                $orderLine->lineValue,
                $orderLine->specialOfferCode != null ? self::YES : self::NO,
                $orderLine->specialOfferCode,
                $orderLine->vat->code,
                $orderLine->vat->rate,
                $orderLine->vat->value,
            ];
        }

        // Remove order type column if toggle quantity type is disabled
        if (!$this->productHelper->isToggleQuantityType()) {
            foreach ($data as &$row) {
                unset($row[28]);
            }
        }

        return $this->generateCsv($data);
    }
}
