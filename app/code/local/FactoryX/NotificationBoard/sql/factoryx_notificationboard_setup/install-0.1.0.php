<?php
/**
 * Who:  Alvin Nguyen
 * When: 2/02/15
 * Why:
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('factoryx_notificationboard/notification'))
    ->addColumn('notification_id',Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true,
    ))
    ->addColumn('notification_title',Varien_Db_Ddl_Table::TYPE_VARCHAR,256,array(
        'nullable' => false
    ))
    ->addColumn('message',Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false
    ))
    ->addColumn('status',Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable' => false
    ))
    ->addColumn('start_date',Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable' => true
    ))
    ->addColumn('end_date',Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable' => true
    ));

$installer->getConnection()->createTable($table);

$installer->endSetup();