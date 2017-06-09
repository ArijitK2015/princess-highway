<?php
/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

// System Configuration update
$envConfig = array(
    "default" => array(
        "factoryx_instagram/module_options/enable_jquery" => '0'
    )
);
/** @var Mage_Core_Model_Config $coreConfig */
$coreConfig = Mage::getModel('core/config');
foreach ($envConfig["default"] as $path => $val) {
    $coreConfig->saveConfig($path, $val, 'default', 0);
}

$installer->endSetup();