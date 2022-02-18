<?php

namespace Accord\Integration\Model;

use Accord\Api\Helper\AttributeManager;

class CustomerSearch
{
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $criteriaBuilder;
    /**
     * @var \Magento\Customer\Model\ResourceModel\CustomerRepository
     */
    private $customerRepository;

    public function __construct(
        \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder,
        \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository
    ) {
        $this->criteriaBuilder = $criteriaBuilder;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param \Accord\Integration\Api\Response\GetUser\AssociatedCustomer $customer
     * @return \Magento\Customer\Api\Data\CustomerInterface[] | \Magento\Customer\Model\Data\Customer[]
     */
    public function getMagentoUsersByCustomer(\Accord\Integration\Api\Response\GetUser\AssociatedCustomer $customer)
    {
        $this->criteriaBuilder
            ->addFilter(AttributeManager::CODE, $customer->customerCode, 'eq')
            ->addFilter(AttributeManager::TYPE, \Accord\Customer\Helper\Customer\Customer::ACCORD_TYPE_CUSTOMER, 'eq');

        return $this->customerRepository->getList($this->criteriaBuilder->create())->getItems();
    }

    /**
     * @param \Accord\Integration\Api\Response\GetUser\AssociatedCustomer $customer
     * @return bool
     */
    public function isExists(\Accord\Integration\Api\Response\GetUser\AssociatedCustomer $customer)
    {
        return (bool)$this->getMagentoUsersByCustomer($customer);
    }

}
