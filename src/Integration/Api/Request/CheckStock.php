<?php
namespace Accord\Integration\Api\Request;

class CheckStock extends Request
{

    /**
     * @param array $data
     * @return array
     */
    protected function convert($data)
    {
        if (!$data) {
            throw new RequestException('data is empty', 0, $this);
        }
        if (!isset($data['customerCode'])) {
            throw new RequestException('customerCode is empty', 0, $this);
        }
        if (isset($data['depot']) && !is_string($data['depot'])) {
            throw new RequestException('depotCode is not valid', 0, $this);
        }
        if (!isset($data['cart'])) {
            throw new RequestException('cart must be set', 0, $this);
        }
        if (!is_array($data['cart'])) {
            throw new RequestException('cart must be array', 0, $this);
        }
        if (!count($data['cart'])) {
            throw new RequestException('cart is empty', 0, $this);
        }
        foreach ($data['cart'] as &$item) {
            $item = $this->convertCartItem($item);
        }

        return $data;
    }

    /**
     * @param array $item
     * @return mixed
     */
    public function convertCartItem($item)
    {
        if (!is_array($item)) {
            throw new RequestException('cart item must be array', 0, $this);
        }
        if (!isset($item['productSku']) || !$item['productSku']) {
            throw new RequestException('productSku is empty', 0, $this);
        }
        if (!isset($item['quantity'])) {
            throw new RequestException('quantity is empty', 0, $this);
        }
        if (!is_int($item['quantity'])) {
            throw new RequestException('quantity is invalid', 0, $this);
        }
        if (!isset($item['quantityType'])) {
            throw new RequestException('quantityType is empty', 0, $this);
        }
        if (!in_array($item['quantityType'], ['cases', 'singles'])) {
            throw new RequestException('quantityType is invalid', 0, $this);
        }

        return $item;
    }

}
