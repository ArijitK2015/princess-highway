<?php

$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('shippedfrom/account_product');

// create the table if it doesn't exist
if ($installer->getConnection()->isTableExists($tableName) != true) {
    $table = $installer->getConnection()
        ->newTable($tableName)
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Entity Id')
        ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Type')
        ->addColumn('group', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Group')
        ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Product Id')
        ->addColumn('associated_shipping_method', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Associated Shipping Method')
        ->setComment('Account Product');

    $installer->getConnection()->createTable($table);
}

$installer->endSetup();