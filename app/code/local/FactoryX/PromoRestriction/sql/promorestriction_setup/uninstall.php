<?php

$installer = $this;
$installer->startSetup();

/**
 * Delete table 'fx_promo_restriction'
 */
if (Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('promorestriction/restriction')) ) {
    $installer
        ->getConnection()
        ->dropTable($this->getTable('promorestriction/restriction'));
}

$installer->endSetup();
