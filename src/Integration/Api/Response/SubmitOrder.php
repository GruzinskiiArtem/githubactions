<?php

namespace Accord\Integration\Api\Response;

/**
 * @property-read number $orderNumber
 * @property-read string $depot
 * @property-read string $createdDate
 * @property-read string $orderID
 */
class SubmitOrder extends Response
{
    /**
     * @return void
     */
    protected function validate()
    {
        if (!isset($this->orderNumber)) {
            throw $this->error('orderNumber is empty');
        }

        if (!isset($this->depot)) {
            throw $this->error('depot is empty');
        }

        if (!isset($this->createdDate)) {
            throw $this->error('createdDate is empty');
        }

        if (!isset($this->orderID)) {
            throw $this->error('orderID is empty');
        }
    }
}
