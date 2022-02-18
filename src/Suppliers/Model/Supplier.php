<?php
namespace Accord\Suppliers\Model;

use Accord\Suppliers\Api\Data\SupplierInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Supplier extends AbstractModel implements SupplierInterface, IdentityInterface
{
    const ENTITY = 'supplier';

    const CACHE_TAG = 'ac_suppliers';

    protected $_cacheTag = 'ac_supplier';

    protected $_eventPrefix = 'ac_supplier';

    protected $_idFieldName = self::ID;

    /**
     * Initialize Supplier model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Accord\Suppliers\Model\ResourceModel\Supplier');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getCode()
    {
        return $this->_getData(self::CODE);
    }

    public function getName()
    {
        return $this->_getData(self::NAME);
    }

    public function getCreatedAt()
    {
        return new \DateTime($this->_getData(self::CREATED_AT));
    }

    public function getUpdatedAt()
    {
        return new \DateTime($this->_getData(self::UPDATED_AT));
    }

    public function setCode($code)
    {
        $this->setData(self::CODE, $code);
    }

    public function setName($name)
    {
        $this->setData(self::NAME, $name);
    }

}
