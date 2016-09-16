<?php

$installer = $this;
$installer->startSetup();

// Add a custom_css column to the homepage table
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('homepage/homepage'), 'theme', 'text'
    );

// changed 'text default "all"' to 'text'
// BLOB and TEXT columns cannot have DEFAULT value

$installer->endSetup();
