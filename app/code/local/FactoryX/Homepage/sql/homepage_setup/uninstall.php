<?php

$installer = $this;
$installer->startSetup();

if (Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('homepage/store')) ) {
    $installer
        ->getConnection()
        ->dropTable($this->getTable('homepage/store'));
}

if (Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('homepage/image')) ) {
    $installer
        ->getConnection()
        ->dropTable($this->getTable('homepage/image'));
}

if (Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('homepage/homepage')) ) {
    $installer
        ->getConnection()
        ->dropTable($this->getTable('homepage/homepage'));
}

$installer->endSetup();
