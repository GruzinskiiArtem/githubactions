<?php

namespace Accord\Integration\Helper;

use Accord\Integration\Api\Commands\ApproveUser;
use Accord\Integration\Api\Commands\GetUser;
use Accord\Integration\Api\Request\RequestInterface;

class User extends \Accord\Integration\Helper\Api
{
    const CACHE_PREFIX = 'getUser:';
    const CACHE_LIFETIME = 3600;
    
    const ASSOCIATED_CUSTOMERT_IS_EMPTY_IN_MAGENTO = 4;
    const CREDIT_STATUS_ON_STOP = 'Stop';

    protected $existsCustomers = [];

    /**
     * @var array
     */
    private $paymentMethodsRegistry = [];

    /**
     * @param $data
     * @param RequestInterface|null $request
     * @return \Accord\Integration\Api\Response\EmptyResponse
     */
    public function approveUser($data, RequestInterface $request = null)
    {
        if (!$request) {
            $request = $this->objectManager->get(\Accord\Integration\Api\Request\ApproveUser::class);
        }
        $response = $this->objectManager->get(\Accord\Integration\Api\Response\EmptyResponse::class);
        return (new ApproveUser($this->client, $request, $response))->execute($data);
    }

    /**
     * @param $data
     * @param RequestInterface|null $request
     * @return \Accord\Integration\Api\Response\ApproveUserHeadOffice
     */
    public function approveUserHeadOffice($data, RequestInterface $request = null)
    {
        if (!$request) {
            $request = $this->objectManager->get(\Accord\Integration\Api\Request\ApproveUser::class);
        }
        $response = $this->objectManager->get(\Accord\Integration\Api\Response\ApproveUserHeadOffice::class);
        return (new ApproveUser($this->client, $request, $response))->execute($data);
    }

    /**
     * @param \Magento\Customer\Model\Customer | mixed | \Accord\Integration\Api\Request\GetUser $data
     * @param RequestInterface | null | \Accord\Integration\Api\Request\GetUser $request
     * @return \Accord\Integration\Api\Response\GetUser | \Accord\Integration\Api\Response\ResponseInterface
     */
    public function getUser($data, RequestInterface $request = null)
    {
        if (!$request) {
            $request = $this->objectManager->get(\Accord\Integration\Api\Request\GetUser::class);
        }
        $response = $this->objectManager->get(\Accord\Integration\Api\Response\GetUser::class);
        return (new GetUser($this->client, $request, $response))->execute($data);
    }

    /**
     * @param RequestInterface $request
     * @param bool $updateCache
     * @return \Accord\Integration\Api\Response\GetUser | \Accord\Integration\Api\Response\ResponseInterface
     */
    public function getUserUseCache(RequestInterface $request, $updateCache = false)
    {
        /**
         * @see \Accord\Integration\Helper\User::getUser
         */
        return $this->useCache('getUser', $request, self::CACHE_PREFIX, self::CACHE_LIFETIME, $updateCache);
    }

    /**
     * @param string $userCode
     * @param string $userType
     * @param bool $updateCache
     * @return \Accord\Integration\Api\Response\GetUser|\Accord\Integration\Api\Response\ResponseInterface
     */
    public function getUserByData($userCode, $userType, $updateCache = false)
    {
        /** @var \Accord\Integration\Api\Request\User $request */
        $request = $this->objectManager->get(\Accord\Integration\Api\Request\User::class);
        $request->setData([
            'userCode' => $userCode,
            'userType' => $userType
        ]);
        return $this->getUserUseCache($request, $updateCache);
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     * @param bool $updateCache
     * @return \Accord\Integration\Api\Response\GetUser | \Accord\Integration\Api\Response\ResponseInterface
     */
    public function getUserByCustomer(\Magento\Customer\Model\Customer $customer, $updateCache = false)
    {
        $request = $this->objectManager->get(\Accord\Integration\Api\Request\GetUser::class);
        $request->setData($customer);
        return $this->getUserUseCache($request, $updateCache);
    }

    public function getUserByCustomerInMagento(\Magento\Customer\Model\Customer $customer, $updateCache = false)
    {
        $getUser = $this->getUserByCustomer($customer, $updateCache);

        $count = 0;
        foreach ($getUser->getCustomers() as $customer) {
            if ($this->isExists($customer)) {
                $count++;
            } else {
                $customer->isExists = false;
            }
        }

        if (!$count) {
            throw new \Exception('associatedCustomers is empty in Magento', self::ASSOCIATED_CUSTOMERT_IS_EMPTY_IN_MAGENTO);
        }

        return $getUser;
    }

    protected function isExists(\Accord\Integration\Api\Response\GetUser\AssociatedCustomer $customer)
    {
        if (isset($this->existsCustomers[$customer->customerCode])) {
            return $this->existsCustomers[$customer->customerCode];
        }

        /**
         * @var \Accord\Integration\Model\CustomerSearch $customerSearch
         */
        $customerSearch = $this->objectManager->get(\Accord\Integration\Model\CustomerSearch::class);
        $result = $customerSearch->isExists($customer);

        $this->existsCustomers[$customer->customerCode] = $result;
        return $result;
    }

    /**
     * @param string $customerCode
     * @return bool
     */
    public function isCustomerOnStop($customerCode)
    {
        $user = $this->getUserByData($customerCode, \Accord\Customer\Helper\Customer\Customer::ACCORD_TYPE_CUSTOMER);
        $creditStatus = $user->getData()['associatedCustomers'][0]['creditStatus'] ?? '';

        return $creditStatus === self::CREDIT_STATUS_ON_STOP;
    }

    /**
     * @param string $customerCode
     *
     * @return array
     */
    public function getAllowedPaymentMethods(string $customerCode): array
    {
        if (!isset($this->paymentMethodsRegistry[$customerCode])) {
            $user = $this->getUserByData(
                $customerCode,
                \Accord\Customer\Helper\Customer\Customer::ACCORD_TYPE_CUSTOMER
            );

            $methods = array_map(
                static function ($item) {
                    return $item['paymentMethod'] ?? '';
                },
                $user->getFirstCustomer()->paymentMethods
            );

            $this->paymentMethodsRegistry[$customerCode] = array_unique(array_filter($methods));
        }

        return $this->paymentMethodsRegistry[$customerCode];
    }
}
