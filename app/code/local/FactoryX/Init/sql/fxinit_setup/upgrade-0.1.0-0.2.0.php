<?php
/**
loads in store locations
*/

$email_content = "";

$installer = $this;
$installer->startSetup();

$helper = Mage::helper('fxinit');

//Import Store Locations
$path = Mage::getBaseDir().'/app/code/local/FactoryX/Init/sql/mysqldump/'.'gmapstrlocator.sql';
if (file_exists($path)) {
	$sql = file_get_contents($path);
	$installer->run($sql);
	$email_content .= "Script ran for Store Locations import<br/>";
}else{
	$email_content .= "Cannot find Store Locations dump<br/>";
}

//mail('raphael@factoryx.com.au','fx install',$email_content);

$installer->endSetup();

?>