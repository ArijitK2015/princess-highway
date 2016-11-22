<?php

$installer = $this;
$installer->startSetup();

if (Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('factoryx_notificationboard/notification')) ) {
    $installer
        ->getConnection()
        ->dropTable($this->getTable('factoryx_notificationboard/notification'));
}

$installer->endSetup();
