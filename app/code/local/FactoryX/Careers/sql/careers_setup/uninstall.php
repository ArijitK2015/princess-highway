<?php

$installer = $this;
$installer->startSetup();

if (Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('careers/careers')) ) {
    $installer
        ->getConnection()
        ->dropTable($this->getTable('careers/careers'));
}

$installer->endSetup();
