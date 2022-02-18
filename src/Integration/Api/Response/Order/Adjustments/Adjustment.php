<?php

namespace Accord\Integration\Api\Response\Order\Adjustments;

/**
 * @property-read string $headerText Optional
 * @property-read number $headerValue Optional
 * @property-read Details\Detail[] $details Optional
 * @property-read string $footerText Optional
 * @property-read number $footerValue Optional
 */
class Adjustment extends \Accord\Integration\Api\Response\Item
{
    protected function validate()
    {
        if (!isset($this->headerText)) {
            $this->headerText = '';
        }

        if (!isset($this->headerValue)) {
            $this->headerValue = 0;
        }

        if (!isset($this->footerText)) {
            $this->footerText = '';
        }

        if (!isset($this->footerValue)) {
            $this->footerValue = 0;
        }

        if (!isset($this->details)) {
            $this->details = [];
        }

        if (!is_string($this->headerText)) {
            throw $this->error('headerText is not specified');
        }

        if (!is_numeric($this->headerValue)) {
            throw $this->error('headerValue should be decimal');
        }

        if (!is_string($this->footerText)) {
            throw $this->error('footerText is not specified');
        }

        if (!is_numeric($this->footerValue)) {
            throw $this->error('footerValue should be decimal');
        }

        $this->initDetails();
    }

    public function initDetails()
    {
        /** @var array $details */
        $details = $this->details;
        $this->details = [];

        foreach ($details as $detail) {
            $this->details[] = new Details\Detail($detail);
        }
    }
}
