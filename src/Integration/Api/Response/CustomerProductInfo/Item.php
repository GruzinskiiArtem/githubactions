<?php
namespace Accord\Integration\Api\Response\CustomerProductInfo;

use Accord\Integration\Api\Response\ResponseException;

/**
 * @property-read string $productSku
 * @property-read string $whichPrice
 * @property-read float $rsp
 * @property-read array $wsps
 * @property-read int $stockCase
 * @property-read int $stockSgl
 * @property-read array $lastOrdered (optional)
 * @property-read array $substituteProductSkus (optional)
 * @property-read array $lineMsgs (optional)
 */
class Item extends \Accord\Integration\Api\Response\Item
{
    const WHICH_PRICE_NORMAL = 'N';
    const WHICH_PRICE_CONTRACT = 'C';
    const WHICH_PRICE_PROMOTION = 'P';

    /**
     * @var Wsps[]
     */
    protected $_wsps = [];

    /**
     * @var LastOrdered
     */
    protected $_lastOrdered = null;

    protected function validate()
    {
        if (!$this->productSku) {
            throw $this->error('productSku is not specified');
        }

        if (!isset($this->whichPrice)) {
            $this->whichPrice = self::WHICH_PRICE_NORMAL;
        }

        if (!isset($this->rsp)) {
            $this->rsp = 0.0;
        }

        if (!is_array($this->wsps)) {
            throw $this->error('wsps must be array');
        }
        if (!count($this->wsps)) {
            throw $this->error('wsps is empty');
        }
        $this->_wsps = [];
        foreach ($this->wsps as $data) {
            $this->_wsps[] = new Wsps($data);
        }

        if (!is_int($this->stockCase)) {
            throw $this->error('stockCase must be integer');
        }
        if (!is_int($this->stockSgl)) {
            throw $this->error('stockSgl must be integer');
        }

        if (isset($this->lastOrdered['quantity'], $this->lastOrdered['quantityType'], $this->lastOrdered['date'])) {
            $this->_lastOrdered = new LastOrdered($this->lastOrdered);
        }

        if (!isset($this->substituteProductSkus)) {
            $this->substituteProductSkus = [];
        }
        if (!is_array($this->substituteProductSkus)) {
            throw $this->error('substituteProductSkus must be array');
        }

        if (!isset($this->lineMsgs)) {
            $this->lineMsgs = [];
        }
        if (!is_array($this->lineMsgs)) {
            throw $this->error('lineMsgs must be array');
        }
    }

    /**
     * @return Wsps[]
     */
    public function getWsps()
    {
        return $this->_wsps;
    }

    /**
     * @return LastOrdered
     */
    public function getLastOrdered()
    {
        return $this->_lastOrdered;
    }

    /**
     * @return Wsps
     * @throws \Exception
     */
    public function getSingleWsp()
    {
        foreach ($this->getWsps() as $item) {
            if ($item->case == Wsps::WSP_SINGLE) {
                return $item;
            }
        }
        throw new \Exception('Item not found');
    }

    /**
     * @return Wsps
     * @throws \Exception
     */
    public function getCaseWsp()
    {
        foreach ($this->getWsps() as $item) {
            if ($item->case == Wsps::WSP_CASE) {
                return $item;
            }
        }
        throw new \Exception('Item not found');
    }

    /**
     * @return bool
     */
    public function hasPromotionPrice(): bool
    {
        return $this->whichPrice === self::WHICH_PRICE_PROMOTION;
    }

    /**
     * @return bool
     */
    public function hasNormalPrice(): bool
    {
        return $this->whichPrice === self::WHICH_PRICE_NORMAL;
    }

    /**
     * @return bool
     */
    public function hasContractPrice(): bool
    {
        return $this->whichPrice === self::WHICH_PRICE_CONTRACT;
    }
}
