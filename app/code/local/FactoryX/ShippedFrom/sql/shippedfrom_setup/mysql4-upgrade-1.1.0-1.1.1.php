<?php
/* @var $this Mage_Eav_Model_Entity_Setup */

Mage::helper('shippedfrom')->log(sprintf("upgrading %s", get_class($this)));

$installer = $this;
$installer->startSetup();

// get table schema
$read = Mage::getSingleton('core/resource')->getConnection('core_read');
$table_schema = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/dbname');

// check if column exists and add if rquired
$column_name = "shipped_by";
$table_name = "sales_flat_shipment";
$sql = sprintf("select * from information_schema.columns where table_schema='%s' and column_name='%s' and table_name='%s';", $table_schema, $column_name, $table_name); 
$result = $read->query($sql);
$result = $result->fetchAll();
if (count($result) == 0) {
    Mage::helper('shippedfrom')->log(sprintf("add '%s' column to '%s'", $column_name, $table_name));
    // alter table sales_flat_shipment add column shipped_by varchar(100);
    $installer->run(sprintf("alter table %s add column %s varchar(100);", $table_name, $column_name));
}

// check if column exists and add if rquired
$column_name = "shipped_by";
$table_name = "sales_flat_shipment_grid";
$sql = sprintf("select * from information_schema.columns where table_schema='%s' and column_name='%s' and table_name='%s';", $table_schema, $column_name, $table_name); 
$result = $read->query($sql);
$result = $result->fetchAll();
if (count($result) == 0) {
    Mage::helper('shippedfrom')->log(sprintf("add '%s' column to '%s'", $column_name, $table_name));
    // alter table sales_flat_shipment_grid add column shipped_by varchar(100);
    $installer->run(sprintf("alter table %s add column %s varchar(100);", $table_name, $column_name));
}

$installer->endSetup();