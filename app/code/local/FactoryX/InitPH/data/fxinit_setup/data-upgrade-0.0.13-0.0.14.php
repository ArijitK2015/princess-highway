<?php

/** @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/** @var FactoryX_InitPH_Helper_Data $helper */
$helper = Mage::helper('initph');

// Generate attributes
$helper->generateAttributes();

$installer->endSetup();