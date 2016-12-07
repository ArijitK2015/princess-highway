<?php

/** @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/** @var FactoryX_InitPH_Helper_Data $helper */
$helper = Mage::helper('initph');

// Reset categories
$path = sprintf("%s/app/code/local/FactoryX/InitPH/sql/resources/reset_categories.sql", Mage::getBaseDir());
if (file_exists($path)) {
    $sql = file_get_contents($path);
    $installer->run($sql);
}

// Import categories
$path = sprintf("%s/app/code/local/FactoryX/InitPH/sql/resources/import_categories.txt", Mage::getBaseDir());
if (file_exists($path)) {
    $helper->createCategoryTree($path);
}
else {
    $helper->log(sprintf("invalid path '%s', could not init cats", $path) );
}

$installer->endSetup();