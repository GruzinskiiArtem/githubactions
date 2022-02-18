<?php

namespace Accord\Integration\Api\Response\Order\Adjustments\Details;

/**
 * @property-read string $detailText Optional
 * @property-read number $detailValue Optional
 */
class Detail extends \Accord\Integration\Api\Response\Item
{
    protected function validate()
    {
        if (!isset($this->detailText)) {
            $this->detailText = '';
        }

        if (!isset($this->detailValue)) {
            $this->detailValue = 0;
        }

        if (!is_string($this->detailText)) {
            throw $this->error('detailText is not specified');
        }

        if (!is_numeric($this->detailValue)) {
            throw $this->error('detailValue should be decimal');
        }
    }
}
