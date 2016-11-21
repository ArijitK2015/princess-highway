<?php

$installer = $this;
$installer->startSetup();

if (Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('customersurvey/results')) ) {
    $installer
        ->getConnection()
        ->dropTable($this->getTable('customersurvey/results'));
}

if (Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('customersurvey/questions')) ) {
    $installer
        ->getConnection()
        ->dropTable($this->getTable('customersurvey/questions'));
}

if (Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('customersurvey/survey')) ) {
    $installer
        ->getConnection()
        ->dropTable($this->getTable('customersurvey/survey'));
}

$installer->endSetup();
