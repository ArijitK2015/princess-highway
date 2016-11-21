<?php

$installer = $this;
$installer->startSetup();

// show_look_number
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('lookbook/lookbook'), 'site_css', 'text default NULL'
    );

$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('lookbook/lookbook'), 'facebook_css', 'text default NULL'
    );


$installer->endSetup();