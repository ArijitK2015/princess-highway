<?php
/**
loads in admin roles and users
*/

$email_content = "";

$installer = $this;
$installer->startSetup();

$helper = Mage::helper('fxinit');

//Import Admin roles and users
$path = Mage::getBaseDir().'/app/code/local/FactoryX/Init/sql/mysqldump/'.'admin.sql';
if (file_exists($path)) {
	$sql = file_get_contents($path);
	$installer->run($sql);
	$email_content .= "Script ran for Admin roles and users import<br/>";
}else{
	$email_content .= "Cannot find Admin roles and users dump<br/>";
}

//mail('raphael@factoryx.com.au','fx install',$email_content);

$installer->endSetup();

?>