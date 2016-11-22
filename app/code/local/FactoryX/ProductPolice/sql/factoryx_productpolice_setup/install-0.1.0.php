<?php
/**
 * Who:  Alvin Nguyen
 * When: 1/10/2014
 * Why:  
 */ 
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('factoryx_productpolice/item'))
    ->addColumn('item_id',Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ))
    ->addColumn('product_id',Varien_Db_Ddl_Table::TYPE_INTEGER,null)
    ->addColumn('created_at',Varien_Db_Ddl_Table::TYPE_TIMESTAMP,null);

$installer->getConnection()->createTable($table);

$installer->endSetup();