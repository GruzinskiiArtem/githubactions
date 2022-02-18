<?php

namespace Accord\Integration\Api\Response;

use Accord\Integration\Api\Response\Invoices\Item as InvoiceItem;

/**
 * Class Invoices
 *
 * @package Accord\Integration\Api\Response
 */
class Invoices extends Response
{
    /**
     * @var InvoiceItem[]
     */
    protected $items = [];

    /**
     * @return void
     */
    protected function validate()
    {
        $data = $this->getData();

        if (!is_array($data)) {
            throw $this->error('data must be array');
        }

        $this->items = [];
        foreach ($data as $item) {
            $this->items[] = new InvoiceItem($item, $this);
        }
    }

    /**
     * @return InvoiceItem[]
     */
    public function getItems()
    {
        return $this->items;
    }
}
