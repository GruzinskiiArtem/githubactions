<?php

namespace Accord\Suppliers\Setup;

use Accord\General\Setup\UpgradeTrait;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Catalog\Model\Product;

class UpgradeData implements UpgradeDataInterface
{
    use UpgradeTrait;

    /**
     * @var EavSetup
     */
    private $eavSetup;

    /**
     * UpgradeData constructor.
     * @param EavSetup $eavSetup
     * @param array $items
     */
    public function __construct(EavSetup $eavSetup, array $items = [])
    {
        $this->eavSetup = $eavSetup;

        $this->setUpgradeItems($items);
    }
}
