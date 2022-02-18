<?php

namespace Accord\Suppliers\Setup\UpgradeData;

use Accord\Api\Helper\AttributeManager;
use Accord\General\Setup\UpgradeInterface;
use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpdateProductAttributes implements UpgradeInterface
{
    /**
     * @var \Accord\Api\Helper\AttributeManager
     */
    private $attributeManager;
    /**
     * @var EavSetup
     */
    private $eavSetup;
    private $attributeSetName;
    /**
     * @var ModuleDataSetupInterface
     */
    private $setup;

    public function __construct(\Accord\Api\Helper\AttributeManager $attributeManager, EavSetup $eavSetup,ModuleDataSetupInterface $setup)
    {
        $this->attributeManager = $attributeManager;
        $this->eavSetup = $eavSetup;

        $attributeManager = $this->attributeManager;
        $this->attributeSetName = $attributeManager::ATTRIBUTE_SET_NAME;
        $this->setup = $setup;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->updateAttributes($this->getAttributesToUpdate());
    }

    private function getAttributesToUpdate()
    {
        return [
            AttributeManager::SUPPLIER => ['is_visible_on_front' => false],
            AttributeManager::SUPPLIER_ID => ['is_visible_on_front' => false],
        ];
    }

    private function updateAttributes($attributesToUpdate)
    {
        foreach ($attributesToUpdate as $attribute => $data) {
            $this->eavSetup->updateAttribute(Product::ENTITY, $attribute, $data);
        }
    }
}
