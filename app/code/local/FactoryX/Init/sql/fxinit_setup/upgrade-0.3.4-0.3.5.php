<?php
/*
load MagicToolbox_MagicZoom
*/

$email_content = "";

$installer = $this;
$installer->startSetup();

if (Mage::getConfig()->getModuleConfig('MagicToolbox_MagicZoom')->is('active', 'true')) {
    Mage::log("the MagicToolbox_MagicZoom is not active, aborting configuration");
    return;
}

// set core config
$coreConfig = new Mage_Core_Model_Config();
$envConfig = array(
    "default"   => array(
    ),
    "staging"   => array(
    ),
    "prod"      => array(
    )
);

$coreConfig = new Mage_Core_Model_Config();

// load env
foreach($envConfig[FactoryX_Init_Helper_Data::_getEnv()] as $path => $val) {
    Mage::log(sprintf("%s->%s: %s", __METHOD__, $path, $val) );
    $coreConfig->saveConfig($path, $val, 'default', 0);
}

// update
Mage::getConfig()->reinit();
Mage::getConfig()->cleanCache();
Mage::app()->reinitStores();

// Import MagicZoom Settings
$path = sprintf("%s/app/code/local/FactoryX/Init/sql/mysqldump/%s", Mage::getBaseDir(), 'magiczoom.sql');

if (file_exists($path)) {
	$sql = file_get_contents($path);	
	$installer->run($sql);
}
else {
    Mage::log(sprintf("could not find config file '%s'", $path));
}

$installer->endSetup();
?>