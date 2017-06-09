<?php

$installer = $this;
$installer->startSetup();

if (Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('menuimage/store')) ) {
    $installer
        ->getConnection()
        ->dropTable($this->getTable('menuimage/store'));
}

if (Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('menuimage/block')) ) {
    $installer
        ->getConnection()
        ->dropTable($this->getTable('menuimage/block'));
}

if (Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('menuimage/menuimage')) ) {
    $installer
        ->getConnection()
        ->dropTable($this->getTable('menuimage/menuimage'));
}

$installer->endSetup();
