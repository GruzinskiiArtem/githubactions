<?php

namespace Accord\Integration\Api\Response\CustomerProductInfo;

use Accord\Integration\Api\Response\ResponseException;
use Accord\Integration\Api\Response\ResponseInterface;

/**
 * @property-read int $case
 * @property-read float $wsp // Price
 * @property-read float|null $wspPerWtUnit// Price
 * @property-read float $nonPromWsp (optional) // Pre-promotion cases price
 */
class Wsps extends \Accord\Integration\Api\Response\Item
{
    const WSP_SINGLE = 0;
    const WSP_CASE = 1;
    const WSP_CASE_10 = 10;
    public $wspPerWtUnit;

    public function validate()
    {
        if (isset($this->data['wspPerWtUnit'])) {
            $this->wspPerWtUnit = $this->data['wspPerWtUnit'];
        }
        if (!is_int($this->case)) {
            throw $this->error('case must be integer');
        }
        if (!is_numeric($this->wsp)) {
            throw $this->error('wsp must be decimal');
        }
        if (!isset($this->nonPromWsp)) {
            $this->nonPromWsp = null;
        }
        if ($this->nonPromWsp && !is_numeric($this->nonPromWsp)) {
            throw $this->error('nonPromWsp must be decimal');
        }
        if (!is_null($this->wspPerWtUnit) && !is_float($this->wspPerWtUnit)) {
            throw $this->error('wspPerWtUnit must be float or null');
        }
    }

}