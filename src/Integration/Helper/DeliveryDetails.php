<?php

namespace Accord\Integration\Helper;

use Accord\Customer\Helper\Customer\Customer;
use Accord\Customer\Helper\Customer\Customer as CustomerHelper;
use Accord\Integration\Api\CacheInterface;
use Accord\Integration\Api\Client\ClientInterface;
use Accord\Integration\Api\Client\ConfigInterface;
use Accord\Integration\Api\Commands\GetDeliveryDetails;
use Accord\Integration\Api\Request\RequestInterface;
use Accord\Integration\Api\Response\ResponseInterface;
use Accord\General\Helper\Config\Catalog\Checkout;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;

class DeliveryDetails extends \Accord\Integration\Helper\Api
{
    const CACHE_PREFIX = 'getDeliveryDetails:';
    const CACHE_LIFETIME = 360;

    /**
     * @var Checkout
     */
    private $checkoutHelper;

    /**
     * DeliveryDetails constructor.
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param CacheInterface $cache
     * @param ClientInterface $client
     * @param ConfigInterface $config
     * @param Registry $registry
     * @param Checkout $checkoutHelper
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        CacheInterface $cache,
        ClientInterface $client,
        ConfigInterface $config,
        Registry $registry,
        Checkout $checkoutHelper
    ) {
        $this->checkoutHelper = $checkoutHelper;
        parent::__construct($context, $objectManager, $cache, $client, $config, $registry);
    }

    /**
     * @param array|null $data
     * @param RequestInterface|null $request
     * @return \Accord\Integration\Api\Response\GetDeliveryDetails | \Accord\Integration\Api\Response\ResponseInterface
     */
    public function getDeliveryDetails($data, RequestInterface $request = null)
    {
        if (!$request) {
            $request = $this->objectManager->get(\Accord\Integration\Api\Request\GetDeliveryDetails::class);
        }

        $response = $this->objectManager->create(\Accord\Integration\Api\Response\GetDeliveryDetails::class);

        return (new GetDeliveryDetails($this->client, $request, $response))->execute($data);
    }

    /**
     * @param array $data
     * @param bool $updateCache
     *
     * @return \Accord\Integration\Api\Response\GetDeliveryDetails | \Accord\Integration\Api\Response\ResponseInterface
     *
     * @deprecated
     */
    public function getDeliveryDetailsUseCache(array $data, $updateCache = false)
    {
        /**
         * @var \Accord\Integration\Api\Request\GetDeliveryDetails $request
         */
        $request = $this->objectManager->get(\Accord\Integration\Api\Request\GetDeliveryDetails::class);
        $request->setData($data);

        /**
         * @see \Accord\Integration\Helper\DeliveryDetails::getDeliveryDetails
         */
        return $this->useCache('getDeliveryDetails', $request, self::CACHE_PREFIX, self::CACHE_LIFETIME, $updateCache);
    }

    /**
     * @param array $data
     *
     * @return ResponseInterface
     */
    public function getDeliveryDetailsUseRegistry(array $data): ResponseInterface
    {
        return $this->useRegistry('getDeliveryDetails', $data);
    }

    /**
     * @param string|null $customerCode
     * @return \Accord\Integration\Api\Response\GetDeliveryDetails|\Accord\Integration\Api\Response\ResponseInterface
     * @throws RequestException
     */
    public function getDeliveryDetailsForSelectedCustomer($customerCode = null)
    {
        /** @var \Accord\Customer\Helper\Current\User $currentUser */
        $currentUser = $this->objectManager->create(\Accord\Customer\Helper\Current\User::class);
        $customerCode = $customerCode ?? $currentUser->getDataManager()->getCustomerCode();
        $pickupMethod = $currentUser->getDataManager()->getPickupMethod();

        $data = [
            'customerCode' => $customerCode
        ];

        if (
            $pickupMethod &&
            Customer::ACCORD_PICKUP_METHOD_BOTH !== $pickupMethod
        ) {
            $data['pickupMethod'] = $pickupMethod;
        }

        if ($depot = $currentUser->getDataManager()->getDepot()) {
            $data['depot'] = $depot;
        }

        return $this->getDeliveryDetailsUseRegistry($data);
    }

    public function getDefaultDeliveryDate()
    {
        return $this->getDeliveryDetailsForSelectedCustomer()->getDefaultDeliveryDate();
    }

    /**
     * @return \Accord\Integration\Api\Response\GetDeliveryDetails\DeliveryDateAvailable[]
     */
    public function getAvailableDatesForDelivery()
    {
        return $this->getDeliveryDetailsForSelectedCustomer()->deliveryDatesAvailable;
    }

    public function getDeliveryAddress()
    {
        return $this->getDeliveryDetailsForSelectedCustomer()->deliveryAddress;
    }

    /**
     * @param string $deliveryDate
     * @return \Accord\Integration\Api\Response\GetDeliveryDetails\DeliveryDateAvailable\ShippingOption[]
     */
    public function getShippingOptionsByDate($deliveryDate)
    {
        $deliveryDetails = $this->getDeliveryDetailsForSelectedCustomer();

        foreach ($deliveryDetails->deliveryDatesAvailable as $deliveryDateAvailable) {
            $date = $deliveryDateAvailable
                ->deliveryDate
                ->format(\Accord\Checkout\Model\DeliveryConfigProvider::DATE_FORMAT);

            if ($this->isDatesEqual($deliveryDate, $date)) {
                return $deliveryDateAvailable->shippingOption;
            }
        }

        return [];
    }

    /**
     * @param string $deliveryDate
     * @param float $orderValue
     * @return \Accord\Integration\Api\Response\GetDeliveryDetails\DeliveryDateAvailable\ShippingOption|null
     */
    public function getApplicableShippingOption($deliveryDate, $orderValue)
    {
        $shippingOptions = $this->getApplicableShippingOptions($deliveryDate, $orderValue);

        return !empty($shippingOptions)
            ? $shippingOptions[0]
            : null;
    }

    /**
     * @param string $deliveryDate
     * @param float $orderValue
     * @return \Accord\Integration\Api\Response\GetDeliveryDetails\DeliveryDateAvailable\ShippingOption[]
     */
    public function getApplicableShippingOptions($deliveryDate, $orderValue)
    {
        $shippingOptions = $this->getShippingOptionsByDate($deliveryDate);
        $shippingOptions = $this->prepareApplicableShippingOptions($shippingOptions, $orderValue);

        return $shippingOptions;
    }

    /**
     * @param string $deliveryDate
     * @param float $orderValue
     * @return \Accord\Integration\Api\Response\GetDeliveryDetails\DeliveryDateAvailable\ShippingOption|null
     */
    public function getShippingOptionForUpsell($deliveryDate, $orderValue)
    {
        $shippingOptions = $this->getApplicableShippingOptions($deliveryDate, $orderValue);
        $applicableShippingOption = $this->getApplicableShippingOption($deliveryDate, $orderValue);
        $shippingOptionForUpsell = null;

        /** @var \Accord\Integration\Api\Response\GetDeliveryDetails\DeliveryDateAvailable\ShippingOption $shippingOption */
        foreach ($shippingOptions as $shippingOption) {
            if ($shippingOption->maxOrderValue > $applicableShippingOption->maxOrderValue) {
                $shippingOptionForUpsell = $shippingOption;
                break;
            }
        }

        if ($shippingOptionForUpsell
            && $shippingOptionForUpsell->deliveryCharge < $applicableShippingOption->deliveryCharge
        ) {
            return $shippingOptionForUpsell;
        }

        return null;
    }

    /**
     * @param float $orderValue
     * @return \Accord\Integration\Api\Response\GetDeliveryDetails\DeliveryDateAvailable[]
     */
    public function getApplicableDatesForDelivery($orderValue)
    {
        /** @var \Accord\Customer\Helper\Current\User $currentUser */
        $currentUser = $this->objectManager->create(\Accord\Customer\Helper\Current\User::class);
        $applicableDates = [];
        $futureEndDays = $this->checkoutHelper->getFutureDeliveryDatePeriod();
        $endDay = strtotime('+' . $futureEndDays . 'days', strtotime(date('Y-m-d 23:59:59')));
        $deliveryDetails = $this->getDeliveryDetailsForSelectedCustomer();

        $deliveryDatesAvailable = array_filter(
            $deliveryDetails->deliveryDatesAvailable,
            function ($element) use ($endDay) {
                return $element->deliveryDate->getTimestamp() <= $endDay;
            }
        );

        if (!is_null($deliveryDetails->simpleShippingOption)) {
            $maxOrderValue = $deliveryDetails->simpleShippingOption->maxOrderValue;

            if ($maxOrderValue == 0 || $orderValue <= $maxOrderValue) {
                $applicableDates = $deliveryDatesAvailable;
            }
        } else {
            foreach ($deliveryDatesAvailable as $deliveryDateAvailable) {
                $deliveryDate = $deliveryDateAvailable
                    ->deliveryDate
                    ->format(\Accord\Checkout\Model\DeliveryConfigProvider::DATE_FORMAT);

                if (!is_null($this->getApplicableShippingOption($deliveryDate, $orderValue)) ||
                    CustomerHelper::ACCORD_PICKUP_METHOD_COLLECTION === $currentUser->getPickupMethod()
                ) {
                    $applicableDates[] = $deliveryDateAvailable;
                }
            }
        }

        return $applicableDates;
    }

    /**
     * @param string $deliveryDate
     * @param float $orderValue
     * @return bool
     */
    public function isDeliveryDateApplicable($deliveryDate, $orderValue)
    {
        return !is_null($this->getApplicableShippingOption($deliveryDate, $orderValue));
    }

    /**
     * @param string $date1
     * @param string $date2
     * @return bool
     */
    public function isDatesEqual($date1, $date2)
    {
        return strcmp($date1, $date2) == 0;
    }

    /**
     * @param \Accord\Integration\Api\Response\GetDeliveryDetails\DeliveryDateAvailable\ShippingOption[] $shippingOptions
     * @param float $orderValue
     * @return \Accord\Integration\Api\Response\GetDeliveryDetails\DeliveryDateAvailable\ShippingOption[]
     */
    protected function prepareApplicableShippingOptions($shippingOptions, $orderValue)
    {
        $shippingOptions = array_filter($shippingOptions, function ($shippingOption) use ($orderValue) {
            /** @var \Accord\Integration\Api\Response\GetDeliveryDetails\DeliveryDateAvailable\ShippingOption $shippingOption */
            return $shippingOption->maxOrderValue > $orderValue;
        });
        $shippingOptions = array_values($shippingOptions);

        usort($shippingOptions, function ($a, $b) {
            /** @var \Accord\Integration\Api\Response\GetDeliveryDetails\DeliveryDateAvailable\ShippingOption $a */
            /** @var \Accord\Integration\Api\Response\GetDeliveryDetails\DeliveryDateAvailable\ShippingOption $b */
            if ($a->maxOrderValue == $b->maxOrderValue) {
                if ($a->deliveryCharge == $b->deliveryCharge) {
                    return 0;
                }
                return ($a->deliveryCharge < $b->deliveryCharge) ? -1 : 1;
            }
            return ($a->maxOrderValue < $b->maxOrderValue) ? -1 : 1;
        });

        return $shippingOptions;
    }
}
