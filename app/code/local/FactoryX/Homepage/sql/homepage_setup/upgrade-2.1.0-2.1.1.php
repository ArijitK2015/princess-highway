<?php

$installer = $this;
$installer->startSetup();

// Add a custom_css column to the homepage table
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('homepage/homepage'), 'custom_css', 'text default NULL'
    );

$installer->endSetup();