<?php


namespace Accord\Integration\Helper;


class Url extends \Magento\Framework\View\Element\Template
{
    /**
     * @param \Accord\Integration\Api\Response\Invoices\Item $item
     * @return string
     */
    public function getDownloadUrl(\Accord\Integration\Api\Response\Invoices\Item $item)
    {
        return $this->getUrl('user/dashboard/download', [
            'type' => 'invoice',
            'depot' => $item->depot,
            'invoice' => $item->invoice,
            'date' => $item->getInvoiceDate()->format('Y-m-d\TH:i:s'),
        ]);
    }

    public function getCsvDownloadUrl(\Accord\Integration\Api\Response\Invoices\Item $item)
    {
        return $this->getUrl('user/dashboard/csvdownload', [
            'orderId' => $item->orderId
        ]);
    }

    public function getDownloadLinks(\Accord\Integration\Api\Response\Invoices\Item $invoice)
    {
        return $this->getPdfLink($invoice).$this->getCsvLink($invoice);
    }

    public function getPdfLink(\Accord\Integration\Api\Response\Invoices\Item $invoice)
    {
        $txtLink = $this->getDownloadUrl($invoice);

        $htmlTxtLink = <<<EOF
<a href="$txtLink" class="action view">
    <span>PDF</span>
</a>
EOF;

        return $htmlTxtLink;
    }

    public function getCsvLink(\Accord\Integration\Api\Response\Invoices\Item $invoice)
    {
        $isOrderNo = (!empty($invoice->orderNo) && !empty($invoice->orderId));
        $titleCsvDownload = $isOrderNo ? '' : 'CSV invoice unavailable for download';
        $hrefCsvDownload = $isOrderNo  ? $this->getCsvDownloadUrl($invoice) : 'javascript:void(0);';
        $classCsvDownload = $isOrderNo ? '' : 'download-csv_disable';

        $htmlCsvLink= <<<EOF
<a title="$titleCsvDownload" href="$hrefCsvDownload" class="action view $classCsvDownload">
    <span>CSV</span>
</a>
EOF;

        return $htmlCsvLink;
    }

}