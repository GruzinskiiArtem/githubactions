<?php

namespace Accord\Integration\Api\Response;

use Accord\Integration\Api\Response\Transactions\Item as TransactionsItem;

/**
 * Class Transactions
 *
 * @package Accord\Integration\Api\Response
 */
class Transactions extends Response
{
    /**
     * @var TransactionsItem[]
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
            $this->items[] = new TransactionsItem($item, $this);
        }
    }

    /**
     * @return TransactionsItem[]
     */
    public function getItems()
    {
        return $this->items;
    }
}
