<?php

$installer = $this;
$installer->startSetup();

if (Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('couponvalidation/log')) ) {
    $installer
        ->getConnection()
        ->dropTable($this->getTable('couponvalidation/log'));
}

$installer->endSetup();
