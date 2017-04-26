<?php

$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('shippedfrom/orders');

// create the table if it doesn't exist
if ($installer->getConnection()->isTableExists($tableName) != true) {
    $table = $installer->getConnection()
        ->newTable($tableName)
        ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Schedule Id')
        ->addColumn('ap_order_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Auspost Order Id')
        ->setComment('Auspost Orders');

    $installer->getConnection()->createTable($table);
}

$installer->endSetup();