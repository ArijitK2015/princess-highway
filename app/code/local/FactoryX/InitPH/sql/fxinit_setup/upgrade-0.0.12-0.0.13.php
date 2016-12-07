<?php
/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$envConfig = array(
    "default" => array(
        "checkoutdiscount/config/remove_discount_box"                  => '0',
    )
);
/** @var Mage_Core_Model_Config $coreConfig */

$coreConfig = Mage::getModel('core/config');
// load default
foreach($envConfig["default"] as $path => $val) {
    $coreConfig->saveConfig($path, $val, 'default', 0);
}


$installer->endSetup();