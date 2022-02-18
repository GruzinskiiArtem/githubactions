<?php
namespace Accord\Suppliers\Plugin\Catalog\Product;

use Accord\Suppliers\Api\Data\SupplierInterface;
use Accord\Suppliers\Model\Supplier;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\ObjectManager;
use Accord\Api\Helper\AttributeManager;

class CheckSupplier
{

    public function beforeSave(Product $product)
    {
        $this->updateEmptySupplier($product);
        $this->updateSupplier($product);
    }

    /**
     * @param Product $product
     * @return Product
     */
    protected function updateSupplier(Product $product)
    {
        $productSupplierCode = $product->getData(AttributeManager::SUPPLIER_ID);
        if (empty($productSupplierCode)) {
            return $product;
        }

        $supplier = $this->getSupplierModel()->load($productSupplierCode, SupplierInterface::CODE);
        $supplierId = $supplier->getId();
        if ($supplierId) {
            $product->setData(AttributeManager::SUPPLIER, $supplierId);
        }

        return $product;
    }


    /**
     * @param Product $product
     * @return Product
     */
    protected function updateEmptySupplier(Product $product)
    {
        if (empty($product->getData(AttributeManager::SUPPLIER_ID))) {
            $product->setData(AttributeManager::SUPPLIER, 0);
        }

        return $product;
    }

    /**
     * @return Supplier
     */
    protected function getSupplierModel()
    {
        return ObjectManager::getInstance()->create('Accord\Suppliers\Model\Supplier');
    }

}
