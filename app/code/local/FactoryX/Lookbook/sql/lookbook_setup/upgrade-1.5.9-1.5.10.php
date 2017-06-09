<?php

$installer = $this;
$installer->startSetup();

// Add a Look Bundle Window Width column to the lookbook table 
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('lookbook/lookbook'), 'look_bundle_window_width', 'varchar(255) default NULL'
    );

$installer->endSetup();