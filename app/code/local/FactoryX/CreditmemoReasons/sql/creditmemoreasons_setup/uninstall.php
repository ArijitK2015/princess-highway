<?php

$installer = $this;
$installer->startSetup();

if (Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('creditmemoreasons/reason')) ) {
    $installer
        ->getConnection()
        ->dropTable($this->getTable('creditmemoreasons/reason'));
}

$setup = new Mage_Sales_Model_Resource_Setup('core_setup');

$setup->removeAttribute('creditmemo', 'reason');
$setup->removeAttribute('creditmemo', 'created_by');

$installer->endSetup();
