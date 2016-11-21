<?php

$installer = $this;
$installer->startSetup();

if (Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('lookbook/lookbook_media')) ) {
    $installer
        ->getConnection()
        ->dropTable($this->getTable('lookbook/lookbook_media'));
}

if (Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('lookbook/store')) ) {
    $installer
        ->getConnection()
        ->dropTable($this->getTable('lookbook/store'));
}

if (Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('lookbook/lookbook')) ) {
    $installer
        ->getConnection()
        ->dropTable($this->getTable('lookbook/lookbook'));
}

$installer->endSetup();
