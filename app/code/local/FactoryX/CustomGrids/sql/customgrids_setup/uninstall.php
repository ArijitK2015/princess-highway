<?php

$installer = $this;
$installer->startSetup();

if (Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('customgrids/column')) ) {
    $installer
        ->getConnection()
        ->dropTable($this->getTable('customgrids/column'));
}

$installer->endSetup();
