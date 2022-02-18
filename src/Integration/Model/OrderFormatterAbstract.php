<?php

namespace Accord\Integration\Model;

abstract class OrderFormatterAbstract implements OrderFormatterInterface
{
    const ORDER_TYPE_BLANK = '<blank>';

    const YES = 'Yes';
    const NO = 'No';

    const INVOICE_TYPE_CREDIT_INVOICE = 'Credit Invoice';
    const INVOICE_TYPE_INVOICE = 'Invoice';

    /** @var \Accord\Integration\Api\Response\Orders */
    protected $order;

    /** @var array */
    const CSV_COLUMN_NAMES = [
        'Invoice No',
        'Invoice Date',
        'Order Number',
        'Order Date',
        'Customer Order Reference Number',
        'Week No**',
        'Customer Account Number',
        'Customer Name',
        'Invoice Type',
        'Invoice Currency',
        'Total Invoice (inc.VAT)',
        'Total Tax',
        'Total Net',
        'Order Type',
        'Depot',
        'Delivery Date',
        'Line Number',
        'Product SKU',
        'EAN Code',
        'TUC Code',
        'Product Description',
        'Unit Type/Size',
        'Product Segment Code',
        'Product Segment Description',
        'Product Group Code',
        'Product Group Description',
        'Weighed Item Indicator',
        'Quantity',
        'Quantity Type',
        'Price',
        'Value',
        'Promo Indicator',
        'Promo Code',
        'VAT Code',
        'VAT Rate',
        'VAT Amount',
    ];

    /**
     * @param $orderValue
     * @return string
     */
    public function getFormattedOrderValue(float $orderValue): string
    {
        return $orderValue >= 0 ? self::INVOICE_TYPE_INVOICE : self::INVOICE_TYPE_CREDIT_INVOICE;
    }

    /**
     * @param float $weight
     * @param int $quantity
     * @return float|int
     */
    public function getFormattedQuantity(float $weight, int $quantity)
    {
        return ($weight > 0) ? $weight : $quantity;
    }

    /**
     * @param int $orderTypeCode
     * @param array $orderTypeMask
     * @return string
     */
    public function getMaskByOrderTypeCode(int $orderTypeCode, array $orderTypeMask): string
    {
        return array_key_exists($orderTypeCode, $orderTypeMask) ?
            $orderTypeMask[$orderTypeCode] :
            self::ORDER_TYPE_BLANK;
    }

    /**
     * @param array $data
     * @return string
     */
    protected function generateCsv(array $data): string
    {
        ob_start();
        $stream = fopen('php://output', 'w');

        foreach ($data as $line) {
            fputcsv($stream, $line);
        }

        fclose($stream);
        $csv = ob_get_contents();
        ob_end_clean();

        return $csv;
    }
}
