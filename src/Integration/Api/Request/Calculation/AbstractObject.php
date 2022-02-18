<?php

namespace Accord\Integration\Api\Request\Calculation;

use Accord\Api\Helper\AttributeManager;
use Magento\Customer\Model\Customer;
use Accord\Integration\Api\Request\RequestException;
use Accord\Customer\Helper\Customer\Customer as CustomerHelper;

abstract class AbstractObject extends AbstractData
{

    /**
     * @param array $data
     * @return array
     */
    protected function convert($data)
    {
        $magCustomer = $data['magCustomer'];
        if (!$magCustomer instanceof Customer) {
            throw new RequestException('Invalid type magCustomer', 0, $this);
        }

        $associatedCustomer = $data['associatedCustomer'];
        if (!$associatedCustomer instanceof \Accord\Integration\Api\Response\GetUser\AssociatedCustomer) {
            throw new RequestException('Invalid type associatedCustomer', 0, $this);
        }

        list($userCode, $userType) = $this->getUserData(
            $magCustomer,
            $associatedCustomer->customerCode,
            filter_var($data['onlyForSelectedUser'], FILTER_VALIDATE_BOOLEAN)
        );

        if (!isset($data['rowsToReturn'])) {
            throw new RequestException('rowsToReturn must be set', 0, $this);
        }

        $data = [
            'userCode' => $userCode,
            'userType' => $userType,
            'rowsToReturn' => $data['rowsToReturn']
        ];

        return parent::convert($data);
    }

    /**
     * @param Customer $customer
     * @param string $selectedUserCode
     * @param bool $onlyForSelectedUser
     *
     * @return array
     */
    private function getUserData(
        Customer $customer,
        string $selectedUserCode,
        bool $onlyForSelectedUser
    ): array {
        $customerType = $customer->getData(AttributeManager::TYPE);
        $customerCode = $customer->getData(AttributeManager::USER_CODE);

        if ($customerType === CustomerHelper::ACCORD_TYPE_HEAD_OFFICE && !$onlyForSelectedUser) {
            return [$customerCode, $customerType];
        }

        if (
            $customerType === CustomerHelper::ACCORD_TYPE_HEAD_OFFICE
            && $onlyForSelectedUser
            || $customerType === CustomerHelper::ACCORD_TYPE_CUSTOMER_GROUP
        ) {
            return [$selectedUserCode, CustomerHelper::ACCORD_TYPE_CUSTOMER];
        }

        return [$selectedUserCode, $customerType];
    }
}