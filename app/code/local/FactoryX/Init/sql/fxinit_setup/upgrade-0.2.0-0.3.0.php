<?php
/**
loads in api admin + admin roles and users
*/

$email_content = "";

$installer = $this;
$installer->startSetup();

$helper = Mage::helper('fxinit');

// import admin roles and users
$path = sprintf("%s/app/code/local/FactoryX/Init/sql/mysqldump/%s", Mage::getBaseDir(), 'admin.sql');
if (file_exists($path)) {
	$sql = file_get_contents($path);
	$installer->run($sql);
    Mage::log(sprintf("loaded sql import: %s", $path) );
}
else{
    Mage::log(sprintf("Error: could not find sql import: %s", $path) );
}

// import API admin roles and users
$path = sprintf("%s/app/code/local/FactoryX/Init/sql/mysqldump/%s", Mage::getBaseDir(), 'admin_api.sql');
if (file_exists($path)) {
	$sql = file_get_contents($path);
	$installer->run($sql);
    Mage::log(sprintf("loaded sql import: %s", $path) );
}
else{
    Mage::log(sprintf("Error: could not find sql import: %s", $path) );
}

$installer->endSetup();
?>