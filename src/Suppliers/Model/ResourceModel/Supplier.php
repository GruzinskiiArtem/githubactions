<?php
namespace Accord\Suppliers\Model\ResourceModel;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime as MagentoDateTime;

class Supplier extends AbstractDb
{

    /**
     * @var MagentoDateTime
     */
    protected $_date;


    /**
     * Supplier constructor.
     * @param Context $context
     * @param MagentoDateTime $date
     * @param null $resourcePrefix
     */
    public function __construct(
        Context $context,
        MagentoDateTime $date,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->_date = $date;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ac_supplier', 'entity_id');
    }

    protected function _beforeSave(AbstractModel $object)
    {

        if (!$this->required($object->getCode()) || !$this->isValidLength($object->getCode(), 10, 1)) {
            throw new LocalizedException(
                __('Not Valid data')
            );
        }

        if (!$this->isValidLength($object->getName(), 30, 0)) {
            throw new LocalizedException(
                __('Not Valid data')
            );
        }

        if ($object->isObjectNew() && !$object->hasCreatedAt()) {
            $object->setCreatedAt($this->_date->gmtDate());
        }

        $object->setUpdatedAt($this->_date->gmtDate());

        return parent::_beforeSave($object);
    }

    /**
     * @return bool
     */
    protected function required($fieldValue): bool
    {
        return (bool) $fieldValue;
    }

    protected function isValidLength($fieldValue, $maxLength = 0, $minLength = 0)
    {
        $result = false;

        if (strlen($fieldValue) >= $minLength && strlen($fieldValue) <= $maxLength) {
            $result = true;
        }

        return $result;
    }

}
