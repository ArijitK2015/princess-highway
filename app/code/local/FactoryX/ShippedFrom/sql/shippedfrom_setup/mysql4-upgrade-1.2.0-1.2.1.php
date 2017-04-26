<?php

$installer = $this;
$installer->startSetup();

// get table schema
$read = Mage::getSingleton('core/resource')->getConnection('core_read');
$table_schema = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/dbname');

// check if column exists and add if rquired
$column_name = "sourced_by";
$table_name = $this->getTable('sales/order_item');
$sql = sprintf("select * from information_schema.columns where table_schema='%s' and column_name='%s' and table_name='%s';", $table_schema, $column_name, $table_name); 
$result = $read->query($sql);
$result = $result->fetchAll();
if (count($result) == 0) {
    Mage::helper('shippedfrom')->log(sprintf("add '%s' column to '%s'", $column_name, $table_name));
    // Add a sourced_from column to the sales_flat_shipment_item table 
    $installer->getConnection()->addColumn($this->getTable('sales/order_item'), 'sourced_by', 'varchar(100)');
    //$installer->run(sprintf("alter table %s add column %s varchar(100);", $table_name, $column_name));
}

$installer->endSetup();