<?php

$installer = $this;
$installer->startSetup();

// Add a slider column to the homepage table
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('homepage/homepage'), 'full_width', 'tinyint(1) NOT NULL default 0'
    );

$installer->endSetup();