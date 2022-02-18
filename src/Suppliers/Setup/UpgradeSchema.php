<?php

namespace Accord\Suppliers\Setup;

use Magento\Framework\DB\Ddl\Table as TableDdl;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '0.1.3', '<')) {
            if ($setup->getConnection()->tableColumnExists($setup->getTable('ac_supplier'), 'status') === false) {
                $setup->getConnection()->addColumn(
                    $setup->getTable('ac_supplier'),
                    'status',
                    [
                        'type' => TableDdl::TYPE_SMALLINT,
                        'length' => 11,
                        'nullable' => false,
                        'default' => 0,
                        'comment' => 'Suppliers Status'
                    ]
                );
            }

            if ($setup->getConnection()->tableColumnExists($setup->getTable('ac_supplier'), 'description') === false) {
                $setup->getConnection()->addColumn(
                    $setup->getTable('ac_supplier'),
                    'description',
                    [
                        'type' => TableDdl::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Suppliers Description'
                    ]
                );
            }

            if ($setup->getConnection()->tableColumnExists($setup->getTable('ac_supplier'), 'image') === false) {
                $setup->getConnection()->addColumn(
                    $setup->getTable('ac_supplier'),
                    'image',
                    [
                        'type' => TableDdl::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Supplier Image'
                    ]
                );
            }

            if ($setup->getConnection()->tableColumnExists($setup->getTable('ac_supplier'), 'content') === false) {
                $setup->getConnection()->addColumn(
                    $setup->getTable('ac_supplier'),
                    'content',
                    [
                        'type' => TableDdl::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Supplier Content'
                    ]
                );
            }

            if ($setup->getConnection()->tableColumnExists($setup->getTable('ac_supplier'), 'supplier_name') === false) {
                $setup->getConnection()->addColumn(
                    $setup->getTable('ac_supplier'),
                    'supplier_name',
                    [
                        'type' => TableDdl::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Suppliers Name Show On Magento'
                    ]
                );
            }
        }

        $setup->endSetup();
    }
}
