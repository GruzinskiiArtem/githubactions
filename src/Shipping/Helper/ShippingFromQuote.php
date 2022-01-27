<?php

namespace Accord\Shipping\Helper;

class ShippingFromQuote
{
    /** @var Shipping */
    protected $shippingHelper;

    /** @var \Magento\Checkout\Model\Session */
    protected $checkoutSession;

    public function __construct(
        \Accord\Shipping\Helper\Shipping $shippingHelper,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->shippingHelper = $shippingHelper;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param null $quote
     * @return null|string
     */
    public function getDespatchDate($quote = null)
    {
        if (is_null($quote)) {
            $quote = $this->checkoutSession->getQuote();
        }
        $methodCode = $this->getShippingMethodCodeFromQuote($quote);
        return $this->shippingHelper->getDespatchDateByShippingMethodCode($methodCode);
    }

    /**
     * @param null $quote
     * @return null|string
     */
    public function getRoute($quote = null)
    {
        if (is_null($quote)) {
            $quote = $this->checkoutSession->getQuote();
        }
        $methodCode = $this->getShippingMethodCodeFromQuote($quote);
        return $this->shippingHelper->getRouteByShippingMethodCode($methodCode);
    }

    /**
     * @param null $quote
     * @return int|null
     */
    public function getCarrierId($quote = null)
    {
        if (is_null($quote)) {
            $quote = $this->checkoutSession->getQuote();
        }
        $methodCode = $this->getShippingMethodCodeFromQuote($quote);
        return $this->shippingHelper->getCarrierIdByShippingMethodCode($methodCode);
    }

    /**
     * @param null $quote
     * @return null|number
     */
    public function getMaxOrderValue($quote = null)
    {
        if (is_null($quote)) {
            $quote = $this->checkoutSession->getQuote();
        }
        $methodCode = $this->getShippingMethodCodeFromQuote($quote);
        return $this->shippingHelper->getMaxOrderValueByShippingMethodCode($methodCode);
    }

    /**
     * @param null $quote
     * @return string
     */
    public function getShippingMethodCodeFromQuote($quote = null)
    {
        if (is_null($quote)) {
            $quote = $this->checkoutSession->getQuote();
        }

        return $quote->getShippingAddress()->getShippingMethod();
    }
}
