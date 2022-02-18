<?php
namespace Accord\Suppliers\Model\Entity\Attribute\Source\Product;

use Accord\Suppliers\Api\Data\SupplierInterface;
use Accord\Suppliers\Model\Supplier;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\App\ObjectManager;

class SupplierSource extends AbstractSource
{
    public function getAllOptions()
    {
        $suppliers = $this->getSupplierModel()->getCollection();
        $suppliers->getSelect()->order(SupplierInterface::NAME);

        $result = [
            [
                'label' => "N/A",
                'value' => "0",
            ]
        ];
        /** @var Supplier $supplier */
        foreach ($suppliers as $supplier) {
            $result[] = [
                'label' => $supplier->getName(),
                'value' => $supplier->getId(),
            ];
        }

        return $result;
    }


    /**
     * @return \Accord\Suppliers\Model\Supplier
     */
    protected function getSupplierModel()
    {
        return ObjectManager::getInstance()->create('Accord\Suppliers\Model\Supplier');
    }

    public function addValueSortToCollection($collection, $dir = \Magento\Framework\Data\Collection::SORT_ORDER_DESC)
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $attributeId = $this->getAttribute()->getId();
        $attributeTable = $this->getAttribute()->getBackend()->getTable();

        $tableName = $attributeCode . '_t';

        $collection->getSelect()->joinLeft(
            [$tableName => $attributeTable],
            "e.entity_id={$tableName}.entity_id" .
            " AND {$tableName}.attribute_id='{$attributeId}'" .
            " AND {$tableName}.store_id='0'",
            []
        );

        $collection->getSelect()->joinLeft(
            ['ac_supplier_t' => 'ac_supplier'],
            "{$tableName}.value = ac_supplier_t.entity_id",
            []
        );

        $collection->getSelect()->order('ac_supplier_t.name ' . $dir);

        return $this;
    }

}
