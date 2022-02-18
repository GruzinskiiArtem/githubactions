<?php

namespace Accord\Suppliers\Setup;

use Accord\Api\Helper\AttributeManager;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{

    /**
     * @var EavSetup
     */
    private $eavSetup;

    public function __construct(EavSetup $eavSetup)
    {

        $this->eavSetup = $eavSetup;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->eavSetup->addAttribute(Product::ENTITY, AttributeManager::SUPPLIER, [
            'type' => 'int',
            'label' => 'Supplier',
            'group' => 'Accord attributes',
            'input' => 'select',
            'required' => false,
            'visible' => true,
            'system' => false,
            'validate_rules' => 'a:0:{}',
            'user_defined' => true,
            'sort_order' => 100,
            'position' => 100,
            'admin_checkout' => 1,
            'filterable' => true,
            'filterable_in_search' => 1,
            'comparable' => true,
            'is_html_allowed_on_front' => true,
            'visible_on_front' => false,
            'is_used_in_grid' => true,
            'is_visible_in_grid' => true,
            'is_filterable_in_grid' => true,
            'used_for_sort_by' => true,
            'default' => 0,
            'source' => 'Accord\Suppliers\Model\Entity\Attribute\Source\Product\SupplierSource',
            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
        ]);
        $setup->endSetup();
    }

}
