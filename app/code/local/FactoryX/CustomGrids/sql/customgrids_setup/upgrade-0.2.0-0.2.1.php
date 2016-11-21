<?php

$installer = $this;
$installer->startSetup();

// Add a popup_referrers column to the contests table
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('customgrids/column'), 'collection_model_type', 'varchar(255) NOT NULL'
    );

$installer->endSetup();