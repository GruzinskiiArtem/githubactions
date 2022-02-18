<?php

namespace Accord\Integration\Api\Request;

use Magento\Customer\Model\Customer;
use Accord\Api\Helper\AttributeManager;

class ApproveUser extends User
{
    /**
     * @param Customer $customer
     * @return array
     */
    protected function convert($customer)
    {
        if (!$customer instanceof Customer) {
            throw new RequestException('Invalid type customer', 0, $this);
        }

        $data = [
            'userCode' => $customer->getData(AttributeManager::CODE),
            'userType' => $customer->getData(AttributeManager::TYPE),
        ];

        return parent::convert($data);
    }

}