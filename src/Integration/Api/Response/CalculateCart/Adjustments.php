<?php
namespace Accord\Integration\Api\Response\CalculateCart;

use Accord\Integration\Api\Response\Item;

/**
 * @property-read string $headerText (optional)
 * @property-read number $headerValue (optional)
 * @property-read string $footerText (optional)
 * @property-read number $footerValue (optional)
 * @property-read array $details
 */
class Adjustments extends Item
{
    /**
     * @var Details[]
     */
    protected $_details = [];

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


        if (!is_numeric($this->headerValue)) {
            throw $this->error('headerValue is invalid');
        }
        if ($this->footerValue && !is_numeric($this->footerValue)) {
            throw $this->error('footerValue is invalid');
        }
        if (!is_numeric($this->headerValue)) {
            throw $this->error('headerValue is invalid');
        }

        if ($this->details && !is_array($this->details)) {
            throw $this->error('details is invalid');
        }

        $this->_details = [];
        foreach ($this->details as $detailData) {
            $this->_details[] = new Details($detailData);
        }
    }

    /**
     * @return Details[]
     */
    public function getDetails()
    {
        return $this->_details;
    }
}