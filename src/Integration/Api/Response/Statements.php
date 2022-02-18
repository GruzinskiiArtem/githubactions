<?php

namespace Accord\Integration\Api\Response;

use Accord\Integration\Api\Response\Statements\Item as StatementsItem;

/**
 * Class Statements
 *
 * @package Accord\Integration\Api\Response
 */
class Statements extends Response
{
    /**
     * @var StatementsItem[]
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
            $this->items[] = new StatementsItem($item, $this);
        }
    }

    /**
     * @return StatementsItem[]
     */
    public function getItems()
    {
        return $this->items;
    }
}
