<?php
/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

// System Configuration update
$envConfig = array(
    "default" => array(
        "design/package/name"       	    	        => 'factoryx',
        "design/theme/locale"       	    	        => 'princess',
        "design/theme/template"       	    	        => 'princess',
        "design/theme/skin"       	    	            => 'princess',
        "design/theme/layout"       	    	        => 'princess',
        "design/theme/default"       	    	        => 'princess',
        "framework/options/enable_bootstrap_css"       	=> '1',
        "framework/options/enable_bootstrap_js"       	=> '1',
        "framework/options/enable_jasny_bootstrap_css"  => '1',
        "framework/options/enable_jasny_bootstrap_js"   => '1',
        "framework/options/enable_fontawesome"          => '1',
        "framework/options/enable_jquery"               => '1'
    )
);
/** @var Mage_Core_Model_Config $coreConfig */
$coreConfig = Mage::getModel('core/config');
// load default
foreach ($envConfig["default"] as $path => $val) {
    $coreConfig->saveConfig($path, $val, 'default', 0);
}

$installer->endSetup();