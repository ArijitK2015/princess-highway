<?php

$installer = $this;
$installer->startSetup();

$installer
    ->getConnection()
    ->dropColumn(
        $installer->getTable('lookbook/lookbook'), FactoryX_Lookbook_Model_Lookbook::SMALL_IMAGE
    );

$installer
    ->getConnection()
    ->dropColumn(
        $installer->getTable('lookbook/lookbook'), FactoryX_Lookbook_Model_Lookbook::SMALL_IMAGE . '_label'
    );

$installer
    ->getConnection()
    ->dropColumn(
        $installer->getTable('lookbook/lookbook'), FactoryX_Lookbook_Model_Lookbook::THUMBNAIL
    );

$installer
    ->getConnection()
    ->dropColumn(
        $installer->getTable('lookbook/lookbook'), FactoryX_Lookbook_Model_Lookbook::THUMBNAIL . '_label'
    );

$installer->endSetup();