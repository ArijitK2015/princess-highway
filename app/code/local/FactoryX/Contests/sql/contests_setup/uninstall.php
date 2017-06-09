<?php

$installer = $this;
$installer->startSetup();

if (Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('contests/store')) ) {
    $installer
        ->getConnection()
        ->dropTable($this->getTable('contests/store'));
}

if (Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('contests/referee')) ) {
    $installer
        ->getConnection()
        ->dropTable($this->getTable('contests/referee'));
}

if (Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('contests/referrer')) ) {
    $installer
        ->getConnection()
        ->dropTable($this->getTable('contests/referrer'));
}

if (Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('contests/contest')) ) {
    $installer
        ->getConnection()
        ->dropTable($this->getTable('contests/contest'));
}

$installer->endSetup();
