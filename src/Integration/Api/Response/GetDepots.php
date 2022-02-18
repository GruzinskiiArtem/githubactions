<?php

namespace Accord\Integration\Api\Response;

use Accord\General\Helper\Config\Catalog\Product as ProductHelper;
use Magento\Framework\App\ObjectManager;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

/**
 * Class GetDepots
 *
 * @package Accord\Integration\Api\Response
 */
final class GetDepots extends Response
{
    /** 
     * @var GetDepots\Depot[] 
     */
    protected $depots = [];

    /**
     * @var ProductHelper
     */
    private $productHelper;

    /**
     * GetDepots constructor.
     *
     * @param ProductHelper $productHelper
     * @param PsrResponseInterface|null $response
     */
    public function __construct(ProductHelper $productHelper, PsrResponseInterface $response = null)
    {
        parent::__construct($response);
        $this->productHelper = $productHelper;
    }

    /**
     * @param null $postcode
     * 
     * @return GetDepots\Depot|null
     */
    public function getDepot($postcode = null)
    {
        if (null === $postcode) {
            foreach ($this->depots as $depot) {
                if (!empty($depot->getCustomerCode())) {
                    return $depot;
                }
            }
        }

        foreach ($this->depots as $depot) {
            if ($depot->postcode == $postcode) {
                return $depot;
            }
        }

        return null;
    }

    /**
     * @param null $customerCode
     * 
     * @return GetDepots\Depot|false
     */
    public function getDepotByCustomerCode($customerCode = null)
    {
        if (!$customerCode) {
            return reset($this->depots);
        }

        foreach ($this->depots as $depot) {
            if ($depot->getCustomerCode() == $customerCode) {
                return $depot;
            }
        }

        return false;
    }

    /**
     * @param string|null $depotCode
     * 
     * @return GetDepots\Depot|false
     */
    public function getDepotByDepotCode($depotCode = null)
    {
        if (!$depotCode) {
            return reset($this->depots);
        }

        foreach ($this->depots as $depot) {
            if ($depot->depot == $depotCode) {
                return $depot;
            }
        }

        return false;
    }

    /**
     * @return GetDepots\Depot[]
     */
    public function getAllDepots()
    {
        return $this->depots;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return count($this->depots);
    }

    /**
     * @return bool
     */
    public function isMultipleDepots()
    {
        return count($this->depots) > 1;
    }

    /**
     * @return void
     */
    protected function validate()
    {
        foreach ($this->getData() as $depot) {
            $depotCandidate = new GetDepots\Depot($this->getProductHelper(), $depot);

            if ($depotCandidate->isNewApi() && $this->getProductHelper()->isCatalogIncludeVat()) {
                if ($depotCandidate->hasGuestVatInclusive()) {
                    $this->depots[] = $depotCandidate;
                }
            } else {
                $this->depots[] = $depotCandidate;
            }
        }
    }

    /**
     * Dependencies not loading while cache used
     *
     * @return ProductHelper
     */
    private function getProductHelper(): ProductHelper
    {
        if (!$this->productHelper) {
            $this->productHelper = ObjectManager::getInstance()->create(ProductHelper::class);
        }

        return $this->productHelper;
    }
}
