<?php

declare(strict_types=1);

namespace Accord\Shipping\Model\Carrier;

use Accord\Customer\Helper\Current\User;
use Accord\Customer\Helper\Customer\Customer;
use Accord\General\Helper\Price;
use Accord\Integration\Api\Response\GetDeliveryDetails\SimpleShippingOption;
use Accord\Integration\Helper\DeliveryDetails;
use Accord\Shipping\Helper\Shipping;
use Exception;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use Psr\Log\LoggerInterface;

class Base extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'accordbase';

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var ResultFactory
     */
    private $rateResultFactory;
    /**
     * @var MethodFactory
     */
    private $rateMethodFactory;

    /** @var User */
    protected $currentUser;

    /** @var Session */
    protected $checkoutSession;

    /** @var DeliveryDetails */
    protected $deliveryDetailsProvider;

    /** @var Shipping */
    protected $shippingHelper;

    /** @var Price */
    protected $priceHelper;

    /**
     * Base constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param User $currentUser
     * @param Session $checkoutSession
     * @param DeliveryDetails $deliveryDetailsProvider
     * @param Shipping $shippingHelper
     * @param Price $priceHelper
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        User $currentUser,
        Session $checkoutSession,
        DeliveryDetails $deliveryDetailsProvider,
        Shipping $shippingHelper,
        Price $priceHelper,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->currentUser = $currentUser;
        $this->checkoutSession = $checkoutSession;
        $this->deliveryDetailsProvider = $deliveryDetailsProvider;
        $this->shippingHelper = $shippingHelper;
        $this->priceHelper = $priceHelper;
    }


    /**
     * @param RateRequest $request
     *
     * @return false|Result
     *
     * @api
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $result = $this->rateResultFactory->create();

        /** @var Method $method */
        if ($shippingMethod = $this->getShippingMethod()) {
            $method = $this->rateMethodFactory->create();
            $method->setData($shippingMethod);
            $result->append($method);
        }

        return $result;
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     *
     * @api
     */
    public function getAllowedMethods(): array
    {
        return [
            $this->_code => $this->getConfigData('name')
        ];
    }

    /**
     * @return array|null
     */
    protected function getShippingMethod(): ?array
    {
        $deliveryDetails = $this->currentUser->getDeliveryDetails();

        if (!is_null($deliveryDetails->simpleShippingOption)) {
            return $this->prepareSimpleShippingOption($deliveryDetails->simpleShippingOption);
        }

        $shippingOption = $this->deliveryDetailsProvider->getApplicableShippingOption(
            $this->getSelectedDeliveryDate(),
            $this->getOrderValue()
        );

        if (is_null($shippingOption) && $this->currentUser->getPickupMethod() !== Customer::ACCORD_PICKUP_METHOD_COLLECTION) {
            return null;
        }

        return [
            'carrier' => $this->_code,
            'carrier_title' => '',
            'method' => $shippingOption ? $this->shippingHelper->getShippingMethodCodeByShippingOption($shippingOption) : '',
            'method_title' => $shippingOption ? $shippingOption->carrierName : '',
            'price' => $shippingOption ? $this->priceHelper->getConvertedPrice($shippingOption->deliveryCharge) : ''
        ];
    }

    /**
     * @param SimpleShippingOption $simpleShippingOption
     *
     * @return array
     */
    protected function prepareSimpleShippingOption(SimpleShippingOption $simpleShippingOption): array
    {
        return [
            'carrier' => $this->_code,
            'carrier_title' => '',
            'method' => $this->_code,
            'method_title' => $this->getConfigData('name'),
            'price' => $this->priceHelper->getConvertedPrice(
                $this->getOrderValue() < $simpleShippingOption->maxOrderValue
                    ? $simpleShippingOption->deliveryCharge
                    : 0
            ),
        ];
    }

    /**
     * @return string
     */
    protected function getSelectedDeliveryDate(): string
    {
        return $this->currentUser->getDeliveryDate();
    }

    /**
     * @return Quote|null
     */
    protected function getQuote(): ?Quote
    {
        try {
            return $this->checkoutSession->getQuote();
        } catch (Exception $e) {
            $this->_logger->critical($e);

            return null;
        }
    }

    /**
     * @return float
     */
    protected function getOrderValue(): float
    {
        $quote = $this->getQuote();

        if (!$quote) {
            return 0;
        }

        return (float) $quote->getSubtotal();
    }
}
