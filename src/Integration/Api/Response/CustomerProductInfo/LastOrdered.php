<?php
namespace Accord\Integration\Api\Response\CustomerProductInfo;

use Accord\Integration\Api\Response\ResponseException;
use Accord\Integration\Api\Response\ResponseInterface;

/**
 * Class LastOrdered
 * @package Accord\Integration\Api\Response\CustomerProductInfo
 * @property-read int $quantity
 * @property-read string $quantityType
 * @property-read datetime $date
 */
class LastOrdered extends \Accord\Integration\Api\Response\Item implements \JsonSerializable
{

    public function validate()
    {
        if (!is_int($this->quantity)) {
            throw $this->error('quantity must be integer');
        }

        if (!$this->quantityType) {
            throw $this->error('quantityType is not specified');
        }

        if (!$this->date) {
            throw $this->error('date is not specified');
        }
    }

    public function getTimestamp()
    {
        return strtotime($this->date);
    }

    public function __toString()
    {
        return $this->quantity . ' ' . $this->quantityType . ' on ' . date('d/m/y', $this->getTimestamp());
    }

    /**
     * @inheritdoc
     */
    function jsonSerialize()
    {
        return $this->__toString();
    }
}