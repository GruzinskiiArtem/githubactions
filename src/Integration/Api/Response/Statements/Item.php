<?php
namespace Accord\Integration\Api\Response\Statements;

/**
 * @property-read datetime $statementDate
 * @property-read string $accountCode
 * @property-read string $weekNo
 * @property-read string $statementNo
 * @property-read string companyCode
 * @property-read string $ledgerCode
 * @property-read string $salesLedgerCode
 */
class Item extends \Accord\Integration\Api\Response\Item
{

    public function getStatementDate()
    {
        return new \DateTime($this->statementDate);
    }

    protected function validate()
    {
        if (!$this->statementDate) {
            throw $this->error('statementDate is not specified');
        }

        if (!$this->accountCode) {
            throw $this->error('accountCode is not specified');
        }

        if (!$this->weekNo) {
            throw $this->error('weekNo is not specified');
        }

        if (!$this->statementNo) {
            throw $this->error('statementNo is not specified');
        }

        if (!$this->companyCode) {
            throw $this->error('companyCode is not specified');
        }

        if (!$this->ledgerCode) {
            throw $this->error('ledgerCode is not specified');
        }

        if (!$this->salesLedgerCode) {
            throw $this->error('salesLedgerCode is not specified');
        }
    }
}