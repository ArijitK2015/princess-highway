<?php
/**
 * Who:  Alvin Nguyen
 * When: 1/10/2014
 * Why:  
 */ 
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('factoryx_productpolice/item'),
        'error_message',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable'  => true,
            'default'   => null,
            'comment'   => 'Error Message'
        ));

$installer->endSetup();