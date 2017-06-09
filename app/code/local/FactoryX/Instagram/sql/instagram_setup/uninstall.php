<?php

$installer = $this;
$installer->startSetup();

if (Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('instagram/instagramlog')) ) {
    $installer
        ->getConnection()
        ->dropTable($this->getTable('instagram/instagramlog'));
}

if (Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('instagram/instagramimage')) ) {
    $installer
        ->getConnection()
        ->dropTable($this->getTable('instagram/instagramimage'));
}

if (Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('instagram/instagramlist')) ) {
    $installer
        ->getConnection()
        ->dropTable($this->getTable('instagram/instagramlist'));
}

$setup = new Mage_Catalog_Model_Resource_Setup('core_setup');

$setup->removeAttribute('catalog_product', 'instagram_hashtag');

$installer->endSetup();
