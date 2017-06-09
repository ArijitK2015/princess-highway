<?php

$installer = $this;
$installer->startSetup();

// Add a slider_nav_style column to the homepage table
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('homepage/homepage'), 'slider_nav_style', 'varchar(255) default NULL'
    );

// Add a slider_pagination_style column to the homepage table
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('homepage/homepage'), 'slider_pagination_style', 'varchar(255) default NULL'
    );
$installer->endSetup();