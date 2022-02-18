<?php

declare(strict_types=1);

namespace Accord\Integration\Model\Config\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\App\ResourceConnection\Proxy;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * Class UpsellAndCrosssell
 */
class UpsellAndCrosssell extends Value
{
    private const TABLE_NAME = 'catalog_product_link';

    /**
     * @var Proxy
     */
    private $proxy;

    /**
     * UpsellAndCrosssell constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param Proxy $proxy
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        Proxy $proxy,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
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
        $connection->delete($connection->getTableName(self::TABLE_NAME));

        return parent::afterSave();
    }
}
