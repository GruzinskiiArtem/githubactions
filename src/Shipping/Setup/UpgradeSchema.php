<?php

namespace Accord\Shipping\Setup;

class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface
{
    /**
     * @inheritdoc
     */
    public function upgrade(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '0.1.1', '<')) {
            $tableName = $setup->getTable('sales_order');
            $fieldName = 'shipping_method';
            if ($setup->getConnection()->tableColumnExists(
                $tableName,
                $fieldName
            )) {
                $setup->getConnection()->modifyColumn(
                    $tableName,
                    $fieldName,
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 128,
                        'nullable' => true,
                        'comment' => 'Shipping Method'
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '0.1.0', '<')) {
            $tableName = $setup->getTable('quote_address');
            $fieldName = 'shipping_method';
            if ($setup->getConnection()->tableColumnExists(
                $tableName,
                $fieldName
            )) {
                $setup->getConnection()->modifyColumn(
                    $tableName,
                    $fieldName,
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 128,
                        'nullable' => true,
                        'comment' => 'Shipping Method'
                    ]
                );
            }
        }

        $setup->endSetup();
    }
}
