<?php

namespace Accord\Integration\Helper;

use Accord\Integration\Api\Request\RequestInterface;

/**
 * @see https://wiki.itransition.com/display/AM2/Calculate+Cart
 */
class SubmitOrder extends \Accord\Integration\Helper\Api
{
    const CACHE_PREFIX = 'submitOrder:';

    /**
     * @param array|null $data
     * @param RequestInterface $request
     * @return \Accord\Integration\Api\Response\SubmitOrder | \Accord\Integration\Api\Response\ResponseInterface
     */
    public function submitOrder($data, RequestInterface $request = null)
    {
        if (!$request) {
            $request = $this->objectManager->get(\Accord\Integration\Api\Request\SubmitOrder::class);
        }
        $response = $this->objectManager->get(\Accord\Integration\Api\Response\SubmitOrder::class);
        return (new \Accord\Integration\Api\Commands\SubmitOrder($this->client, $request, $response))->execute($data);
    }

    /**
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @param $deliveryDate
     * @param string $customerRef
     * @return \Accord\Integration\Api\Response\SubmitOrder|null
     */
    public function submitOrderForSelectedCustomer(\Magento\Quote\Api\Data\CartInterface $quote, $deliveryDate, $customerRef = '')
    {
        /** @var \Accord\Integration\Api\Request\SubmitOrderObject $request */
        $request = $this->objectManager->get(\Accord\Integration\Api\Request\SubmitOrderObject::class);

        /** @var \Accord\Customer\Helper\Current\User $currentUser */
        $currentUser = $this->objectManager->create(\Accord\Customer\Helper\Current\User::class);

        $customerCode = $currentUser->getDataManager()->getCustomerCode();
        $pickupMethod = $currentUser->getDataManager()->getPickupMethod();
        $depot = $currentUser->getDataManager()->getDepot();

        $request->setData(
            [
                $quote,
                $customerCode,
                $deliveryDate,
                $customerRef,
                $pickupMethod,
                $depot,
            ]
        );

        return $this->submitOrder(null, $request);
    }

}
