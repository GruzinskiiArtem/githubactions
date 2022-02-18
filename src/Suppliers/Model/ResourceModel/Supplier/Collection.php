<?php
namespace Accord\Suppliers\Model\ResourceModel\Supplier;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Accord\Suppliers\Model\Supplier', 'Accord\Suppliers\Model\ResourceModel\Supplier');
    }
}
