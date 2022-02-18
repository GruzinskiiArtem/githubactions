<?php

namespace Accord\Integration\Api\Response;

use Accord\Integration\Api\Response\CustomerProductInfo\Item as ProductItem;

/**
 * Class CustomerProductInfo
 *
 * @package Accord\Integration\Api\Response
 */
class CustomerProductInfo extends Response
{
    /**
     * @var ProductItem[]
     */
    protected $items = [];

    /**
     * @return void
     */
    protected function validate()
    {
        $data = is_array($this->getData()) ? $this->getData() : [];

        $this->items = array_map(function ($item) {
            return new ProductItem($item, $this);
        }, $data);
    }

    /**
     * @return ProductItem[]
     */
    public function getItems()
    {
        return $this->items;
    }
}
