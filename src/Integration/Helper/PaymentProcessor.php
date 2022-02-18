<?php

declare(strict_types=1);

namespace Accord\Integration\Helper;

use Accord\Api\Helper\Order;
use Accord\Integration\Api\CacheInterface;
use Accord\Integration\Api\Client\ClientInterface;
use Accord\Integration\Api\Client\ConfigInterface;
use Accord\Integration\Api\Commands\PaymentStatus;
use Accord\Integration\Api\Request\RequestInterface;
use Accord\Integration\Api\Response\ResponseInterface;
use Accord\Payment\Model\PaymentRef\PaymentRefHandlersManager;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order\Payment;

/**
 * @link https://wiki.itransition.com/display/AM2/Payment+Status
 */
class PaymentProcessor extends Api
{
    /**
     * @var string
     */
    const STATUS_SUCCESS = 'Success';

    /**
     * @var string
     */
    const STATUS_CANCEL = 'Cancel';

    /**
     * @var Order
     */
    protected $helperOrder;

    /**
     * @var PaymentRefHandlersManager
     */
    private $paymentRefHandlersManager;

    /**
     * PaymentProcessor constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param CacheInterface $cache
     * @param ClientInterface $client
     * @param ConfigInterface $config
     * @param Registry $registry
     * @param Order $helperOrder
     * @param PaymentRefHandlersManager $paymentRefHandlersManager
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        CacheInterface $cache,
        ClientInterface $client,
        ConfigInterface $config,
        Registry $registry,
        Order $helperOrder,
        PaymentRefHandlersManager $paymentRefHandlersManager
    ) {
        parent::__construct(
            $context,
            $objectManager,
            $cache,
            $client,
            $config,
            $registry
        );

        $this->helperOrder = $helperOrder;
        $this->paymentRefHandlersManager = $paymentRefHandlersManager;
    }

    /**
     * @param OrderPaymentInterface|array $data
     * @param RequestInterface|null $request
     * @param string|null $status
     *
     * @return ResponseInterface
     */
    public function sendPaymentStatus(
        $data,
        RequestInterface $request = null,
        $status = null
    ): ResponseInterface {
        if (!$request) {
            $request = $this->objectManager->get(\Accord\Integration\Api\Request\PaymentStatus::class);
        }
        $response = $this->objectManager->get(\Accord\Integration\Api\Response\PaymentStatus::class);
        $command = new PaymentStatus($this->client, $request, $response);
        $data = $this->preparePaymentStatusData($data, $status);

        return $command->execute($data);
    }

    /**
     * @param Payment|array $payment
     * @param string $status
     * @return array|Payment
     */
    protected function preparePaymentStatusData($payment, string $status)
    {
        if (is_array($payment)) {
            return $payment;
        }

        return [
            'depot' => $payment->getOrder()->getData('ac_depot'),
            'orderNumber' => $this->helperOrder->getOrderNumber($payment->getOrder()),
            'createdDate' => $payment->getOrder()->getCreatedAt(),
            'status' => $status,
            'paymentRef' => $this->paymentRefHandlersManager->getFormattedPaymentRef($payment),
        ];
    }

    /**
     * @param Payment $payment
     *
     * @return string
     */
    protected function getPaymentToken(Payment $payment): string
    {
        $token = '';
        $additionalInformation = $payment->getData('additional_information');

        if (!empty($additionalInformation['paypal_express_checkout_token'])) {
            $token = $additionalInformation['paypal_express_checkout_token'];
        } elseif (!empty($additionalInformation['processorAuthorizationCode']))  {
            $token = $additionalInformation['processorAuthorizationCode'];
        }

        return $token;
    }
}
