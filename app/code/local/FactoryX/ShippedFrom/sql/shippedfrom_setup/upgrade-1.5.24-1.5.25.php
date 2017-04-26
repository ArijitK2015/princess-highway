<?php

$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('shippedfrom/cron_log');

// create the table if it doesn't exist
if ($installer->getConnection()->isTableExists($tableName) != true) {
    $table = $installer->getConnection()
        ->newTable($tableName)
        ->addColumn('log_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Log Id')
        ->addColumn('cron_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable'  => false,
            'default'   => FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_INITIALIZED,
        ), 'Cron Name')
        ->addColumn('summary', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Summary')
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable'  => false
        ), 'Created At')
        ->setComment('Cron Log');

    $installer->getConnection()->createTable($table);
}

$installer->endSetup();