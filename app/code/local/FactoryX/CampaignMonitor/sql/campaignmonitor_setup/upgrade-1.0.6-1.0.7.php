<?php
/**
move old configuration values to new path
*/

$installer = $this;
$installer->startSetup();

Mage::helper('campaignmonitor')->log(sprintf("%s", __FILE__));

$moveConfig = array(
    'newsletter/coupon/enable' => 'newsletter/subscription/generate_coupon'
);

$resource = Mage::getSingleton('core/resource');
$dbConn = $resource->getConnection('core_write');

foreach($moveConfig as $oldConfig => $newConfig) {
    Mage::helper('campaignmonitor')->log(sprintf("move config '%s' -> '%s'", $oldConfig, $newConfig));
    // check if exists
    $sql = sprintf("select scope, scope_id, value from core_config_data where path = '%s'", $oldConfig);
    Mage::helper('campaignmonitor')->log(sprintf("sql: %s", $sql));
    $configToMove = $dbConn->fetchAll($sql);
    if (count($configToMove)) {
        foreach($configToMove as $config) {
            $sql = sprintf("replace into core_config_data (scope, scope_id, path, value) values ('%s',%d,'%s','%s')",
                $config['scope'], $config['scope_id'], $newConfig, $config['value']
            );
            Mage::helper('campaignmonitor')->log(sprintf("sql: %s", $sql));
            $dbConn->query($sql);

            $sql = sprintf("delete from core_config_data where scope = '%s' AND path = '%s'", $config['scope'], $oldConfig);
            Mage::helper('campaignmonitor')->log(sprintf("sql: %s", $sql));
            $dbConn->query($sql);
        }
    }
}

$installer->endSetup();