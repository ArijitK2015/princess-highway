<?php

$installer = $this;
$installer->startSetup();

// Add a popup_image_url column to the contests table
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('contests/contest'), 'referee_email_subject', 'varchar(255) default NULL'
    );

$installer->endSetup();