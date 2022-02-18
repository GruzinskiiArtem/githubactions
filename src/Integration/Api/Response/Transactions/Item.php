<?php
namespace Accord\Integration\Api\Response\Transactions;

/**
 * @property-read string $transactionDate
 * @method \DateTime $getTransactionDate
 * @property-read string $customerCode
 * @property-read string $transactionType
 * @property-read string $dueDate
 * @method  \DateTime $getDueDate
 * @property-read string $invoice
 * @property-read string $order
 * @property-read string $documentRef
 * @property-read string $customerRef
 * @property-read string $statementNumber
 * @property-read string $totalValue
 * @property-read string $outstandingValue
 */
class Item extends \Accord\Integration\Api\Response\Item
{
    const CREDIT_RECORD_POSTFIX = 'CR';

    /**
     * @return \DateTime
     */
    public function getTransactionDate()
    {
        return new \DateTime($this->transactionDate);
    }

    /**
     * @return \DateTime
     */
    public function getDueDate()
    {
        return new \DateTime($this->dueDate);
    }

    /**
     * @return float|int
     */
    public function getTotalValue()
    {
        return $this->getAmountValue($this->totalValue);
    }

    /**
     * @return float|int
     */
    public function getOutstandingValue()
    {
        return $this->getAmountValue($this->outstandingValue);
    }

    /**
     * @return bool
     */
    public function isCredit()
    {
        return ($this->getTotalValue() < 0 || $this->getOutstandingValue() < 0);
    }

    protected function validate()
    {
        if (!$this->transactionDate) {
            throw $this->error('transactionDate is not specified');
        }

        if (!is_string($this->customerCode)) {
            throw $this->error('customerCode is invalid');
        }

        if (!$this->transactionType) {
            throw $this->error('transactionType is not specified');
        }

        if (!$this->dueDate) {
            throw $this->error('dueDate is not specified');
        }

        if (!is_int($this->invoice)) {
            throw $this->error('invoice is invalid');
        }

        if (!is_int($this->order)) {
            throw $this->error('order is invalid');
        }

        if (!$this->documentRef) {
            throw $this->error('documentRef is not specified');
        }

        if (!is_string($this->customerRef)) {
            throw $this->error('customerRef is not specified');
        }

        if (!$this->statementNumber) {
            throw $this->error('statementNumber is not specified');
        }

        if (!$this->totalValue) {
            throw $this->error('totalValue is not specified');
        }

        if (!$this->outstandingValue) {
            throw $this->error('outstandingValue is not specified');
        }
    }

    /**
     * @param string $amount
     * @return float|int
     */
    protected function getAmountValue($amount = '')
    {
        $amount = trim($amount);

        if (strpos($amount, self::CREDIT_RECORD_POSTFIX) !== false) {
            return - floatval(str_replace(self::CREDIT_RECORD_POSTFIX, '', $amount));
        } else {
            return floatval($amount);
        }
    }
}
