<?php
namespace Accord\Suppliers\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Accord\Suppliers\Api\Data\SupplierInterface;


class InstallSchema implements InstallSchemaInterface
{


    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $table = $installer->getConnection()->newTable(
            $installer->getTable('ac_supplier')
        )->addColumn(
            SupplierInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        )->addColumn(
            SupplierInterface::CODE,
            Table::TYPE_TEXT,
            10,
            ['nullable' => false],
            'Supplier Code'
        )->addColumn(
            SupplierInterface::NAME,
            Table::TYPE_TEXT,
            30,
            ['default' => ''],
            'Supplier Name'
        )->addColumn(
            SupplierInterface::CREATED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            SupplierInterface::UPDATED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Updated At'
        )->addIndex(
            $installer->getIdxName(
                'ac_supplier',
                [SupplierInterface::CODE],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            [SupplierInterface::CODE],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $installer->getIdxName('ac_supplier', [SupplierInterface::NAME]),
            [SupplierInterface::NAME]
        )->setComment(
            'Suppliers'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
