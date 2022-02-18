<?php

namespace Accord\Integration\Api\Response;

/**
 * @property-read number $subTotal (optional)
 * @property-read number $vatValue (optional)
 * @property-read number $orderValue
 * @property-read number $orderSurchargeValue (optional)
 * @property-read number $orderDiscountValue (optional)
 * @property-read number $deliveryCharge (optional)
 * @property-read number $subTotalExVat (optional)
 * @property-read array $adjustments
 * @property-read array $cart
 * @property-read array $mixMatchQualify
 */
class CalculateCart extends Response
{
    /**
     * @var CalculateCart\Adjustments[]
     */
    protected $_adjustments = [];

    /**
     * @var CalculateCart\Cart[]
     */
    protected $_cart = [];

    /**
     * @var CalculateCart\MixMatchQualify[]
     */
    protected $_mixMatchQualify = [];

    /**
     * @return CalculateCart\Adjustments[]
     */
    public function getAdjustments()
    {
        return $this->_adjustments;
    }

    /**
     * @return CalculateCart\Cart[]
     */
    public function getCart()
    {
        return $this->_cart;
    }

    /**
     * @param string $id
     * @return CalculateCart\Cart|null
     */
    public function getCartItem($id)
    {
        if ($id) {
            foreach ($this->getCart() as $item) {
                if ($item->id === $id || strpos($item->id, $id) !== false) {
                    return $item;
                }
            }
        }

        throw $this->error('Item ' . $id . ' not found');
    }

    /**
     * @return CalculateCart\MixMatchQualify[]
     */
    public function getMixMatchQualify()
    {
        return $this->_mixMatchQualify;
    }

    /**
     * @return void
     */
    protected function validate()
    {
        if (!isset($this->subTotal)) {
            $this->subTotal = 0;
        }

        if (isset($this->subTotalExVat)) {
            $this->subTotal = $this->subTotalExVat;
        }

        if (!isset($this->subTotalExVat)) {
            $this->subTotalExVat = 0;
        }

        if (!isset($this->vatValue)) {
            $this->vatValue = 0;
        }
        if (!isset($this->orderSurchargeValue)) {
            $this->orderSurchargeValue = 0;
        }
        if (!isset($this->orderDiscountValue)) {
            $this->orderDiscountValue = 0;
        }
        if (!isset($this->deliveryCharge)) {
            $this->deliveryCharge = 0;
        }

        if (!is_numeric($this->subTotal)) {
            throw $this->error('subTotal is invalid');
        }
        if (!is_numeric($this->vatValue)) {
            throw $this->error('vatValue is invalid');
        }
        if (!is_numeric($this->orderValue)) {
            throw $this->error('orderValue is invalid');
        }
        if (!is_numeric($this->orderSurchargeValue)) {
            throw $this->error('orderSurchargeValue is invalid');
        }
        if (!is_numeric($this->orderDiscountValue)) {
            throw $this->error('orderDiscountValue is invalid');
        }
        if (!is_numeric($this->deliveryCharge)) {
            throw $this->error('deliveryCharge is invalid');
        }

        if (!is_array($this->adjustments)) {
            throw $this->error('adjustments is invalid');
        }
        $this->_adjustments = [];
        foreach ($this->adjustments as $adjustmentData) {
            $this->_adjustments[] = new CalculateCart\Adjustments($adjustmentData, $this);
        }

        if (!is_array($this->cart)) {
            throw $this->error('cart is invalid');
        }
        $this->_cart = [];
        foreach ($this->cart as $cartData) {
            $this->_cart[] = new CalculateCart\Cart($cartData, $this);
        }

        if (!is_array($this->mixMatchQualify)) {
            throw $this->error('mixMatchQualify is invalid');
        }
        $this->_mixMatchQualify = [];
        foreach ($this->mixMatchQualify as $mixMatchQualifyData) {
            $this->_mixMatchQualify[] = new CalculateCart\MixMatchQualify($mixMatchQualifyData, $this);
        }
    }
}
