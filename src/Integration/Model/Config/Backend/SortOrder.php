<?php

namespace Accord\Integration\Model\Config\Backend;

use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\App\ResourceConnection\Proxy;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Smile\ElasticsuiteVirtualCategory\Model\ResourceModel\Category\Product\Position;

/**
 * Class SortOrder
 */
class SortOrder extends Value
{
    /**
     * @var Proxy
     */
    private $proxy;

    /**
     * SortOrder constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param Proxy $proxy
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        Proxy $proxy,
        array $data = []
    ) {
        $this->proxy = $proxy;

        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @return AbstractModel
     */
    public function afterSave(): AbstractModel
    {
        $value = $this->getValue();

        if (!$this->isValueChanged() || $value) {
            return parent::afterSave();
        }

        $connection = $this->proxy->getConnection();
        $connection->delete($connection->getTableName(Position::TABLE_NAME));

        return parent::afterSave();
    }
}
