<?php
/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

// System Configuration update
$envConfig = array(
    "default" => array(
        "awajaxcatalog/general/enabled" => '1',
        "wajaxcatalog/general/action_type"  => 'scroll',
        "wajaxcatalog/general/backtotop_enabled"  => '1',
        "wajaxcatalog/general/backtotop_label"  => 'Return to top',
    )
);
/** @var Mage_Core_Model_Config $coreConfig */
$coreConfig = Mage::getModel('core/config');
foreach ($envConfig["default"] as $path => $val) {
    $coreConfig->saveConfig($path, $val, 'default', 0);
}

$installer->endSetup();