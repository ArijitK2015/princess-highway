<?php

$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('shippedfrom/shipment_item');

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
        ->addColumn('item_reference', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'item Reference')
        ->addColumn('item_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Item Id')
        ->addColumn('weight', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Weight')
        ->addColumn('schedule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Schedule Id')
        ->setComment('Shipment Items');

    $installer->getConnection()->createTable($table);

    $installer->getConnection()->addForeignKey(
        $installer->getFkName('shippedfrom/shipment_item', 'schedule_id', 'shippedfrom/shipping_queue', 'schedule_id'),
        $installer->getTable('shippedfrom/shipment_item'),
        'schedule_id',
        $installer->getTable('shippedfrom/shipping_queue'),
        'schedule_id'
    );
}

$installer->endSetup();