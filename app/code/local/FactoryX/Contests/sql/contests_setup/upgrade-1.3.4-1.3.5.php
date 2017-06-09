<?php

$installer = $this;
$installer->startSetup();

// Add a popup_image_url column to the contests table
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('contests/contest'), 'popup_image_url', 'varchar(255) default NULL'
    );

$installer->endSetup();