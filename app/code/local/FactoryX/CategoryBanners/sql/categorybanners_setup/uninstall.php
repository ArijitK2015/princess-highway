<?php

$installer = $this;
$installer->startSetup();

if (Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('categorybanners/banner')) ) {
    $installer
        ->getConnection()
        ->dropTable($this->getTable('categorybanners/banner'));
}

$installer->endSetup();
