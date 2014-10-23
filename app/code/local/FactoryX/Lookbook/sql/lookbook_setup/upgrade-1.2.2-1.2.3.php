<?php

$installer = $this;
$installer->startSetup();

// Add a facebook app id column to the lookbook table
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('lookbook/lookbook'), 'facebook_app_id', 'varchar(255) default NULL'
    );

// Add a facebook app secret column to the lookbook table
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('lookbook/lookbook'), 'facebook_app_secret', 'varchar(255) default NULL'
    );

$installer->endSetup();