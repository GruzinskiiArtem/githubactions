<?php

namespace Accord\Integration\Api\Response;

/**
 * Class CheckStock
 *
 * @package Accord\Integration\Api\Response
 */
class CheckStock extends Response
{
    /**
     * @var array
     */
    protected $_cart = [];

    /**
     * @return CheckStock\Cart[]
     */
    public function getCart()
    {
        return $this->_cart;
    }

    /**
     * @return void
     */
    protected function validate()
    {
        if (!is_array($this->getData())) {
            throw $this->error('data is invalid');
        }
        
        $this->_cart = [];
        
        foreach ($this->getData() as $cartData) {
            $this->_cart[] = new CheckStock\Cart($cartData, $this);
        }
    }

    /**
     * @param string $id
     * 
     * @return CheckStock\Cart|null
     */
    public function getCartItem($id)
    {
        foreach ($this->getCart() as $item) {
            if ($item->id == $id) {
                return $item;
            }
        }
        return null;
    }
}
