<?php

namespace Accord\Suppliers\Plugin\Supplier;

use Accord\Api\Helper\AttributeManager;
use Accord\Suppliers\Model\Supplier;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\ObjectManager;

class UpdateProducts
{


    public function afterSave(Supplier $supplier, $result)
    {
        $this->updateProductSupplierAttribute($supplier);
        return $supplier;
    }

    /**
     * @param \Accord\Suppliers\Model\Supplier $object
     */
    protected function updateProductSupplierAttribute(Supplier $object)
    {
        $productCollection = $this->getProductModel()->getCollection();
        $productCollection->addAttributeToFilter(AttributeManager::SUPPLIER_ID, $object->getCode());
        /** @var Product $product */
        foreach ($productCollection as $product) {
            if ($product->getData(AttributeManager::SUPPLIER) == $object->getId()) {
                continue;
            }

            $product->setData(AttributeManager::SUPPLIER, $object->getId());
            $product->getResource()->saveAttribute($product, AttributeManager::SUPPLIER);
        }
    }

    /**
     * @return Product
     */
    protected function getProductModel()
    {
        return ObjectManager::getInstance()->create('Magento\Catalog\Model\Product');
    }
}