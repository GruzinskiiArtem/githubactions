<?php

namespace Accord\Integration\Api\Response;

use Accord\Integration\Api\Response\GetUser\AssociatedCustomer;

/**
 * @property-read bool $allowViewingTransactions
 * @property-read bool $allowViewingStatements
 * @property-read array $associatedCustomers
 */
class GetUser extends Response
{
    const ASSOCIATED_CUSTOMERT_IS_EMPTY = 3;

    /**
     * @var AssociatedCustomer[]
     */
    protected $customers = [];

    /**
     * @return void
     */
    protected function validate()
    {
        if (!isset($this->allowViewingTransactions) || !is_bool($this->allowViewingTransactions)) {
            $this->allowViewingTransactions = false;
        }

        if (!isset($this->allowViewingStatements) || !is_bool($this->allowViewingStatements)) {
            $this->allowViewingStatements = false;
        }

        if (!$this->associatedCustomers) {
            throw $this->error('associatedCustomers is empty', self::ASSOCIATED_CUSTOMERT_IS_EMPTY);
        }

        if (!is_array($this->associatedCustomers)) {
            throw $this->error('associatedCustomers is invalid');
        }

        $this->customers = [];

        foreach ($this->associatedCustomers as $customerData) {
            $this->customers[] = new AssociatedCustomer($customerData, $this);
        }
    }

    /**
     * @return AssociatedCustomer
     */
    public function getFirstCustomer()
    {
        foreach ($this->customers as $customer) {
            if ($customer->isExists && $customer->canOrder()) {
                return $customer;
            }
        }

        return reset($this->customers);
    }

    /**
     * @return AssociatedCustomer[]
     */
    public function getCustomers()
    {
        return $this->customers;
    }

    /**
     * @param $code
     *
     * @return AssociatedCustomer|false
     */
    public function getCustomerByCode($code)
    {
        foreach ($this->customers as $customer) {
            if ($customer->customerCode == $code) {
                return $customer;
            }
        }
        return false;
    }
}
