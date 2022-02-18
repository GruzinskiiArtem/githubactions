<?php

namespace Accord\Integration\Api\Request;

class SubmitOrder extends Request
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
        if (!$data['customerCode']) {
            throw new RequestException('customerCode is empty', 0, $this);
        }

        if (!isset($data['deliveryDate'])) {
            throw new RequestException('deliveryDate must be set', 0, $this);
        }

        /** @var \DateTime $devDate */
        $devDate = $data['deliveryDate'];
        if (!$devDate instanceof \DateTime) { // if ($date < time()) {
            throw new RequestException('deliveryDate is invalid', 0, $this);
        }
        $data['deliveryDate'] = $devDate->format('c');

        if (isset($data['despatchDate'])) {
            /** @var \DateTime $devDate */
            $despatchDate = $data['despatchDate'];
            if (!$despatchDate instanceof \DateTime) {
                throw new RequestException('despatchDate is invalid', 0, $this);
            }
            $data['despatchDate'] = $despatchDate->format('c');
        }

        if (isset($data['route']) && !is_string($data['route'])) {
            throw new RequestException('route should be string', 0, $this);
        }

        if (isset($data['carrierId']) && !is_int($data['carrierId'])) {
            throw new RequestException('carrierId should be int', 0, $this);
        }

        if (!isset($data['user'])) {
            throw new RequestException('user must be set', 0, $this);
        }

        if (!$data['user']) {
            throw new RequestException('user is empty', 0, $this);
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

        if (empty($data['paymentMethod'])) {
            throw new RequestException('paymentMethod is not be empty', 0, $this);
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
        if (!isset($item['freeLine'])) {
            throw new RequestException('freeLine is empty', 0, $this);
        }
        if (!is_bool($item['freeLine'])) {
            throw new RequestException('freeLine is invalid', 0, $this);
        }

        if (isset($item['originalProductSku']) && $item['originalProductSku'] && !is_string($item['originalProductSku'])) {
            throw new RequestException('originalProductSku is invalid', 0, $this);
        }
        if (isset($item['lineMsg']) && $item['lineMsg'] && !is_string($item['lineMsg'])) {
            throw new RequestException('lineMsg is invalid', 0, $this);
        }
        if (isset($item['mixMatchCode']) && $item['mixMatchCode'] && !is_string($item['mixMatchCode'])) {
            throw new RequestException('mixMatchCode is invalid', 0, $this);
        }

        return $item;
    }

}