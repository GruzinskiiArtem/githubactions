<?php

namespace Accord\Integration\Api\Request;

use Accord\Checkout\Block\Cart\Onepage\DeliveryDetails as BlockDeliveryDetails;

class CalculateCartObject extends CalculateCart
{
    /**
     * @var \Accord\Quote\Helper\Item\OptionManager
     */
    protected $optionManager;

    /**
     * CalculateCartObject constructor.
     * @param \Accord\Quote\Helper\Item\OptionManager $optionManager
     * @param array|null $data
     */
    public function __construct(
        \Accord\Quote\Helper\Item\OptionManager $optionManager,
        $data = null
    ) {
        parent::__construct($data);

        $this->optionManager = $optionManager;
    }

    /**
     * TODO add parameters and return type hints
     * @param array $data
     * @return array
     */
    protected function convert($data)
    {
        /**
         * @var \Magento\Quote\Model\Quote $quote
         * @var string $customerCode
         * @var string|null $despatchDate
         * @var string|null $route
         * @var int|null $carrierId
         * @var string|null $pickupMethod
         * @var string|null $depot
         */
        list(
            $quote,
            $customerCode,
            $despatchDate,
            $route,
            $carrierId,
            $pickupMethod,
            $depot
            ) = $data;

        if (!$quote instanceof \Magento\Quote\Model\Quote) {
            throw new RequestException('Cart is invalid', 0, $this);
        }

        $cart = [];
        $finalCart = [];

        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($quote->getAllVisibleItems() as $item) {
            $options = $this->optionManager->getOptionsByItem($item);

            if ($options->getFreeLine()) {
                $finalCart[] = [
                    'id'            => $item->getId(),
                    'productSku'    => $item->getSku(),
                    'quantity'      => (int)$item->getQty(),
                    'quantityType'  => strtolower($options->getQtyType()),
                    'freeLine'      => $options->getFreeLine(),
                    'mixMatchCode'  => $options->getMixMatchCode(),
                ];
            } else {
                $key = $item->getSku() . '_' . strtolower($options->getQtyType());

                if (isset($cart[$key])) {
                    $cart[$key]['id'] .= ',' . $item->getId();
                    $cart[$key]['quantity'] += (int)$item->getQty();
                } else {
                    $cart[$key] = [
                        'id' => $item->getId(),
                        'productSku' => $item->getSku(),
                        'quantity' => (int)$item->getQty(),
                        'quantityType' => strtolower($options->getQtyType()),
                        'freeLine' => false,
                    ];
                }
            }
        }

        $finalCart = array_merge($finalCart, array_values($cart));

        $data = [
            'customerCode' => $customerCode,
            'cart' => $finalCart,
        ];

        if ($quote->getExtShippingInfo()) {
            $data['deliveryDate'] = \DateTime::createFromFormat(
                BlockDeliveryDetails::CREATE_DATE_FORMAT,
                $quote->getExtShippingInfo()
            );
        }

        if ($despatchDate) {
            $data['despatchDate'] = \DateTime::createFromFormat(
                \Accord\Shipping\Helper\Shipping::DESPATCH_DATE_FORMAT,
                $despatchDate
            );
        }

        if ($route) {
            $data['route'] = $route;
        }

        if ($carrierId) {
            $data['carrierId'] = $carrierId;
        }

        if (
            $pickupMethod &&
            \Accord\Customer\Helper\Customer\Customer::ACCORD_PICKUP_METHOD_BOTH !== $pickupMethod
        ) {
            $data['pickupMethod'] = $pickupMethod;
        }

        if ($depot) {
            $data['depot'] = $depot;
        }

        return parent::convert($data);
    }
}
