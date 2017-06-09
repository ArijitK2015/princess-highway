<?php
/**
uninstall
select * from core_resource where code like '%shipped%';
delete from core_resource where code = 'shippedfrom_setup';
alter table sales_flat_shipment drop column shipped_from; 
alter table sales_flat_shipment_grid drop column shipped_from;
select store_code from ustorelocator_location where store_code = 'H00';
delete from ustorelocator_location where store_code = 'H00';
*/
$installer = $this;
//Mage::helper('shippedfrom')->log(sprintf("%s->%s", __METHOD__, get_class($installer)));

$installer->startSetup();

$read = Mage::getSingleton('core/resource')->getConnection('core_read');
$table_schema = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/dbname');
$column_name = "store_code";
$table_name = "ustorelocator_location";
$sql = sprintf("select * from information_schema.columns where table_schema='%s' and column_name='%s' and table_name='%s';", $table_schema, $column_name, $table_name); 
$result = $read->query($sql);
$result = $result->fetchAll();
if (count($result) > 0) {
    $result = $read->query("select store_code from ustorelocator_location where store_code = 'H00';");
    $result = $result->fetchAll();
    if (count($result) == 0) {    
        //Mage::helper('shippedfrom')->log("add 'Default Warehouse'");    
        $installer->run("insert into ustorelocator_location (title, store_code) values('Default Warehouse','H00');");
    }
}
else {
    $installer->run("alter table ustorelocator_location add column store_code varchar(3);");
    $installer->run("insert into ustorelocator_location (title,store_code) values('Default Warehouse','H00');");
}

$column_name = "shipped_from";
$table_name = "sales_flat_shipment";
$sql = sprintf("select * from information_schema.columns where table_schema='%s' and column_name='%s' and table_name='%s';", $table_schema, $column_name, $table_name); 
$result = $read->query($sql);
$result = $result->fetchAll();
if (count($result) == 0) {
    $installer->run("alter table sales_flat_shipment add column shipped_from int(10) unsigned;");
}

$column_name = "shipped_from";
$table_name = "sales_flat_shipment_grid";
$sql = sprintf("select * from information_schema.columns where table_schema='%s' and column_name='%s' and table_name='%s';", $table_schema, $column_name, $table_name); 
$result = $read->query($sql);
$result = $result->fetchAll();
if (count($result) == 0) {
    $installer->run("alter table sales_flat_shipment_grid add column shipped_from int(10) unsigned;");
}

$installer->endSetup();
