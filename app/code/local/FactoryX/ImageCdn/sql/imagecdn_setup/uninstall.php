<?php

$installer = $this;
$installer->startSetup();

if (Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('imagecdn/cachedb')) ) {
    $installer
        ->getConnection()
        ->dropTable($this->getTable('imagecdn/cachedb'));
}

$installer->endSetup();
