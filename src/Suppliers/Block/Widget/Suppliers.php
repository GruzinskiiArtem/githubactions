<?php

declare(strict_types=1);

namespace Accord\Suppliers\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Accord\Api\Helper\AttributeManager;

class Suppliers extends \Accord\ElasticSearch\Block\Widget\ProductsList implements BlockInterface
{
    use \Accord\Catalog\Block\Product\Products;

    const CACHE_KEY_PREFIX = 'ACCORD_BLOCK_BS_';
    const PAGE_VAR_NAME = 'np_suppliers';
    const CACHE_KEY = 'product_suppliers';
    const DEFAULT_COLLECTION_PAGE_SIZE = 12;

    protected $_template = "Accord_Suppliers::widget/product-suppliers.phtml";

    protected function _construct()
    {
        parent::_construct();

        $this->setData('cache_tags', [self::CACHE_KEY]);
        $this->setData('conditions', []);
    }

    protected function _beforeToHtml()
    {
        $this->setProductCollection($this->createCollection());
        return parent::_beforeToHtml();
    }

    public function createCollection()
    {
        $collection = parent::createCollection();
        $pageSize = $this->getNumberLimit() ?: self::DEFAULT_COLLECTION_PAGE_SIZE;

        $collection
            ->addFieldToFilter('supplier', $this->getData('supplier_id'));
        if ($this->getProductId()) {
            $pageSize = $pageSize + 1;
        }
        $collection->setPageSize($pageSize)
            ->setCurPage(1)
            ->setOrder(AttributeManager::ACTIVE_DATE_TS);

        return $collection;
    }

    public function getCacheKeyInfo()
    {
        return array_merge(
            parent::getCacheKeyInfo(),
            [
                $this->getData('label'),
                $this->getData('show_label'),
                $this->getData('label_alignment'),
                $this->getData('label_font_size'),
                $this->getData('products_count'),
                $this->getData('sort'),
                $this->getData('template'),
            ]
        );
    }
}
