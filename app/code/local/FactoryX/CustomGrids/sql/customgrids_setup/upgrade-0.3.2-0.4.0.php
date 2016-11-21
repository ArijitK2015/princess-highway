<?php

$installer = $this;
$installer->startSetup();

// Add a popup_referrers column to the contests table
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('customgrids/column'), 'remove', 'tinyint(1) DEFAULT 0'
    );

$installer->endSetup();