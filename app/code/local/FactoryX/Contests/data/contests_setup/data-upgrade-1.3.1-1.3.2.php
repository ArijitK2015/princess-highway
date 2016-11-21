<?php
$installer = $this;
$installer->startSetup();

// System Configuration update
$envConfig = array(
    "default" => array(
        "contests/options/cron_expr"       	    	        => '*/5 * * * *',
    )
);

$coreConfig = Mage::getModel('core/config');
// load default
foreach($envConfig["default"] as $path => $val) {
    $coreConfig->saveConfig($path, $val, 'default', 0);
}

$installer->endSetup();