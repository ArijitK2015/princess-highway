<?php

$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('shippedfrom/shipping_queue');

// create the table if it doesn't exist
if ($installer->getConnection()->isTableExists($tableName) != true) {
    $table = $installer->getConnection()
        ->newTable($tableName)
        ->addColumn('schedule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Schedule Id')
        ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable'  => false,
            'default'   => FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_INITIALIZED,
        ), 'Status')
        ->addColumn('shipment_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Shipment Id')
        ->addColumn('ap_shipment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Auspost Shipment Id')
        ->addColumn('ap_request_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Auspost Request Id')
        ->addColumn('ap_consignment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Auspost Consignment Id')
        ->addColumn('ap_order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Auspost Order Id')
        ->addColumn('ap_label_uri', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Auspost Label Link')
        ->addColumn('shipped_from', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Shipped From')
        ->addColumn('ap_last_message', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Last message returned from the API')
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable'  => false,
            'default'  => Varien_Db_Ddl_Table::TIMESTAMP_INIT
        ), 'Created At')
        ->setComment('Shipping Queue');

    $installer->getConnection()->createTable($table);
}

$installer->endSetup();