<?php

namespace Accord\Integration\Api\Response;

/**
 * @property-read string $headOfficeCode
 */
class ApproveUserHeadOffice extends Response
{
    /**
     * @return void
     */
    protected function validate()
    {
        if (!isset($this->headOfficeCode) || !$this->headOfficeCode) {
            throw $this->error('headOfficeCode is not specified');
        }
    }
}
