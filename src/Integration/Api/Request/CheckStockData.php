<?php

namespace Accord\Integration\Api\Request;

use Accord\Quote\Helper\Item\OptionManager;

class CheckStockData extends CheckStock
{
    /**
     * @var OptionManager
     */
    private $optionManager;

    /**
     * @param OptionManager $optionManager
     * @param null $data
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
         * @var \Magento\Quote\Model\Quote\Item[] $quote
         * @var string $customerCode
         * @var string $depot
         */
        list($quoteItems, $customerCode, $depot) = $data;

        if (!is_array($quoteItems)) {
            throw new RequestException('Quote items is empty', 0, $this);
        }

        $cart = [];

        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($quoteItems as $item) {
            $options = $this->optionManager->getOptionsByItem($item);

            $cart[] = [
                'id' => $item->getId(),
                'productSku' => $item->getSku(),
                'quantity' => (int)$item->getQty(),
                'quantityType' => strtolower($options->getQtyType()),
            ];
        }

        $requestData = [
            'customerCode' => $customerCode,
            'cart' => $cart,
        ];

        if ($depot) {
            $requestData['depot'] = $depot;
        }

        return parent::convert($requestData);
    }
}
