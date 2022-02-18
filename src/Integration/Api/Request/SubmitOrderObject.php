<?php

namespace Accord\Integration\Api\Request;

use Accord\Catalog\Model\CustomerProduct\Repository;
use Accord\Checkout\Helper\Stock;
use Accord\Customer\Helper\Customer\Customer;
use Accord\General\Helper\Config\Catalog\Product;
use Accord\Integration\Api\Response\CustomerProductInfo\Item;
use Accord\Integration\Model\Provider\OverrideName;
use Accord\Quote\Helper\AccordQuote as AccordQuoteHelper;
use Accord\Quote\Helper\PaymentQuote as PaymentQuoteHelper;
use Accord\Quote\Model\Quote\Item\AccordOption;
use Accord\Shipping\Helper\Shipping as ShippingHelper;
use Accord\Shipping\Helper\ShippingFromQuote as ShippingFromQuoteHelper;
use Exception;
use Magento\Quote\Model\Quote;

/**
 * Class SubmitOrderObject
 */
class SubmitOrderObject extends SubmitOrder
{
    /**
     * @var string
     */
    private const ADDRESS_FIELD_CITY = 'address4';

    /**
     * @var string
     */
    private const ADDRESS_FILED_COUNTY = 'address5';

    /**
     * @var string
     */
    private const DEFAULT_EMAIL = 'default@example.com';

    /**
     * @var string
     */
    private const OPORTEO_ADDRESS_TYPE = 'oporteo-customer-address';

    /**
     * @var AccordQuoteHelper
     */
    protected $accordQuoteHelper;

    /**
     * @var Repository
     */
    protected $customerRepository;

    /**
     * @var PaymentQuoteHelper
     */
    protected $paymentQuoteHelper;

    /**
     * @var ShippingFromQuoteHelper
     */
    protected $shippingFromQuoteHelper;

    /**
     * @var Product
     */
    private $productHelper;

    /**
     * @var OverrideName
     */
    private $overrideNameProvider;

    /**
     * SubmitOrderObject constructor.
     *
     * @param AccordQuoteHelper $accordQuoteHelper
     * @param PaymentQuoteHelper $paymentQuoteHelper
     * @param Repository $customerRepository
     * @param ShippingFromQuoteHelper $shippingFromQuoteHelper
     * @param Product $productHelper
     * @param null $data
     */
    public function __construct(
        AccordQuoteHelper $accordQuoteHelper,
        PaymentQuoteHelper $paymentQuoteHelper,
        Repository $customerRepository,
        ShippingFromQuoteHelper $shippingFromQuoteHelper,
        Product $productHelper,
        OverrideName $overrideNameProvider,
        $data = null
    ) {
        parent::__construct($data);

        $this->accordQuoteHelper = $accordQuoteHelper;
        $this->customerRepository = $customerRepository;
        $this->paymentQuoteHelper = $paymentQuoteHelper;
        $this->shippingFromQuoteHelper = $shippingFromQuoteHelper;
        $this->productHelper = $productHelper;
        $this->overrideNameProvider = $overrideNameProvider;
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws Exception
     */
    protected function convert($data)
    {
        /**
         * @var \Magento\Quote\Model\Quote $quote
         * @var string $customer
         */
        list($quote, $customerCode, $deliveryDate, $customerRef, $pickupMethod, $depot) = $data;

        if (!$quote instanceof \Magento\Quote\Model\Quote) {
            throw new RequestException('Cart is invalid', 0, $this);
        }

        $requiredFields = [
            'customerCode' => $customerCode,
            'deliveryDate' => $deliveryDate,
            'user' => $this->getEmail($quote),
            'cart' => $this->getCartData($quote, $pickupMethod),
            'paymentMethod' => $this->paymentQuoteHelper->getAccordPaymentMethodCode($quote),
            'paymentAmount' => (float) $this->paymentQuoteHelper->getPaymentAmount($quote),
            'overridePhone' => $quote->getShippingAddress()->getTelephone(),
        ];

        $optionalFields = array_filter([
            'route' => $this->getRoute(),
            'carrierId' => $this->getCarrierId(),
            'customerRef' => (string) $customerRef,
            'pickupMethod' => (string) ($pickupMethod === Customer::ACCORD_PICKUP_METHOD_BOTH ? '' : $pickupMethod),
            'depot' => (string) $depot,
            'despatchDate' => $this->getDespatchDate(),
        ]);

        if ($quote->getShippingAddress()->getType() !== self::OPORTEO_ADDRESS_TYPE) {
            $optionalFields = array_merge(
                $optionalFields,
                array_filter([
                    'overrideName' => $this->overrideNameProvider->get($quote),
                    'overrideAddress' => $this->getOverrideShippingAddress($quote),
                    'overridePhone' => $quote->getShippingAddress()->getTelephone(),
                ])
            );
        }

        $data = array_merge($requiredFields, $optionalFields);

        return parent::convert($data);
    }

    /**
     * @param Quote $quote
     * @param string|null $pickupMethod
     *
     * @return array|array[]
     * @throws Exception
     */
    protected function getCartData(Quote $quote, ?string $pickupMethod)
    {
        $accordItemCollection = $this->accordQuoteHelper->getAccordItemData($quote);

        $cartData = array_map(function (AccordOption $itemData) use ($quote, $pickupMethod) {
            $sku = (string) $itemData->getDataByKey('sku');
            $item = $this->customerRepository->loadBySku($sku);

            $requiredFields = [
                'productSku' => $sku,
                'quantity' => $this->getQty($quote, $itemData),
                'quantityType' => strtolower($this->getQtyType($quote, $itemData)),
                'freeLine' => $itemData->getFreeLine(),
            ];

            $optionalFields = array_filter([
                'originalSku' => (string) $itemData->getDataByKey('originalSku'),
                'lineMsg' => $this->productHelper->isLineMessageEnable() ? $itemData->getLineMessage() : '',
                'mixMatchCode' => (string) $itemData->getDataByKey('mixMatchCode'),
                'wsp' => $pickupMethod === Customer::ACCORD_PICKUP_METHOD_COLLECTION
                    ? $this->getWsp($item, $itemData->getQtyType())
                    : null,
            ]);

            return array_merge($requiredFields, $optionalFields);
        }, $accordItemCollection->getItems());

        return array_values($cartData);
    }

    /**
     * Pass quote for children classes
     *
     * @param Quote $quote
     * @param AccordOption $itemData
     *
     * @return int
     */
    protected function getQty(Quote $quote, AccordOption $itemData)
    {
        return (int) $itemData->getDataByKey('qty');
    }

    /**
     * Pass quote for children classes
     *
     * @param Quote $quote
     * @param AccordOption $itemData
     *
     * @return string
     */
    protected function getQtyType(Quote $quote, AccordOption $itemData)
    {
        return $itemData->getQtyType();
    }

    /**
     * @param Quote $quote
     *
     * @return array
     */
    protected function getOverrideShippingAddress($quote)
    {
        $overrideShippingAddress = [];
        $shippingAddress = $quote->getShippingAddress();
        $addressParts = $shippingAddress->getStreet();

        for ($i = 0; $i < count($addressParts); $i++) {
            $overrideShippingAddress['address' . ($i + 1)] = $addressParts[$i];
        }

        $overrideShippingAddress[self::ADDRESS_FIELD_CITY] = $shippingAddress->getCity();

        if ($shippingAddress->getRegion()) {
            $overrideShippingAddress[self::ADDRESS_FILED_COUNTY] = $shippingAddress->getRegion();
        }

        $overrideShippingAddress['postcode'] = $shippingAddress->getPostcode();

        return array_filter($overrideShippingAddress);
    }

    /**
     * @param Quote $quote
     *
     * @return string
     */
    protected function getEmail($quote)
    {
        return $quote->getShippingAddress()->getEmail() ?? self::DEFAULT_EMAIL;
    }

    /**
     * @return \DateTime|false|null
     */
    protected function getDespatchDate()
    {
        $despatchDate = $this->shippingFromQuoteHelper->getDespatchDate();

        if (!$despatchDate) {
            return null;
        }

        return \DateTime::createFromFormat(ShippingHelper::DESPATCH_DATE_FORMAT, $despatchDate);
    }

    /**
     * @return null|string
     */
    protected function getRoute()
    {
        return $this->shippingFromQuoteHelper->getRoute();
    }

    /**
     * @return int|null
     */
    protected function getCarrierId()
    {
        return $this->shippingFromQuoteHelper->getCarrierId();
    }

    /**
     * @param Item $item
     * @param string $qtyType
     *
     * @return float|null
     */
    protected function getWsp(Item $item, string $qtyType)
    {
        $wsp = $qtyType === Stock::TYPE_CASES ? $item->getCaseWsp() : $item->getSingleWsp();

        if (!$wsp || !$wsp->wsp) {
            return null;
        }

        return $wsp->wsp;
    }
}
