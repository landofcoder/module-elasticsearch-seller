<?php
namespace Lof\ElasticsuiteSeller\Setup;

use \Magento\Framework\Setup\InstallSchemaInterface;
use \Magento\Framework\Setup\ModuleContextInterface;
use \Magento\Framework\Setup\SchemaSetupInterface;
use \Magento\Eav\Setup\EavSetup;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * Installs DB schema for the module
     *
     * @param SchemaSetupInterface   $setup   The setup interface
     * @param ModuleContextInterface $context The module Context
     *
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $connection = $setup->getConnection();
        $table      = $setup->getTable('lof_marketplace_seller');

        // Append a column 'is_searchable' into the db.
        $connection->addColumn(
            $table,
            'is_searchable',
            [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                'nullable' => false,
                'default'  => '1',
                'comment'  => 'If seller is searchable',
            ]
        );

        $setup->endSetup();
    }
}
