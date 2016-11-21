<?php

$installer = $this;
$installer->startSetup();

if (Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('picklist_request_log')) ) {
    $installer
        ->getConnection()
        ->dropTable($this->getTable('picklist_request_log'));
}

if (Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('picklist_job_log')) ) {
    $installer
        ->getConnection()
        ->dropTable($this->getTable('picklist_job_log'));
}

$installer->endSetup();
