<?php
/**
uninstall
select * from core_resource where code = '%sales%';
delete from core_resource where code = 'sales_setup';

sales_flat_order_status_history
alter table sales_flat_order_status_history add track_user varchar(50) after comment;
*/

$installer = $this;
$installer->startSetup();
//$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
//$setup->addAttribute('sales_flat_order_status_history', 'track_user', array('type' => 'varchar'));
//$installer->run("alter table sales_flat_order_status_history add track_user varchar(50) after comment;");

$installer->endSetup();
