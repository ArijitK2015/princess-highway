<?php

$installer = $this;
$installer->startSetup();

// Add a custom_css column to the homepage table
$installer
    ->getConnection()
    ->changeColumn(
        $this->getTable('homepage/homepage'), 'theme', 'themes', 'text'
    );

$installer->endSetup();