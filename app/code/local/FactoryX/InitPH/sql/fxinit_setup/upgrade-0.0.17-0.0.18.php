<?php
/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$envConfig = array(
    "default" => array(
        "ajaxcartpopup/general/enabled"                  => '1',
    )
);
/** @var Mage_Core_Model_Config $coreConfig */

$coreConfig = Mage::getModel('core/config');
// load default
foreach($envConfig["default"] as $path => $val) {
    $coreConfig->saveConfig($path, $val, 'default', 0);
}


$installer->endSetup();