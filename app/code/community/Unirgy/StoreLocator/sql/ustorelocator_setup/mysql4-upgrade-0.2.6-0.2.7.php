<?php
/*
@var $this Mage_Eav_Model_Entity_Setup

select * from core_resource where code = 'ustorelocator_setup';
delete from core_resource where code = 'ustorelocator_setup';

update core_resource set version = '0.2.7' where code = 'ustorelocator_setup';
update core_resource set data_version = '0.2.7' where code = 'ustorelocator_setup';

ALTER TABLE ustorelocator_location ADD `store_code` varchar(3) NOT NULL AFTER `location_id`;
*/

$installer = $this;

$installer->startSetup();

$table_schema = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/dbname');
$column_name = "store_code";
$table_name = "ustorelocator_location";
$sql = sprintf("select * from information_schema.columns where table_schema='%s' and column_name='%s' and table_name='%s';", $table_schema, $column_name, $table_name); 

$read = Mage::getSingleton('core/resource')->getConnection('core_read');
$result = $read->query($sql);
$result = $result->fetchAll();
if (count($result) <= 0){
	$installer->run("ALTER TABLE {$this->getTable('ustorelocator_location')} ADD `store_code` varchar(3) NOT NULL AFTER `location_id`;");
}

$installer->endSetup();