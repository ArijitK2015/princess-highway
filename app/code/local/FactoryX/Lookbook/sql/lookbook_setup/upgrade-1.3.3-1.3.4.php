<?php

$installer = $this;
$installer->startSetup();

// Add a slider_nav_style column to the lookbook table
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('lookbook/lookbook'), 'slider_nav_style', 'varchar(255) default NULL'
    );

// Add a slider_pagination_style column to the lookbook table
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('lookbook/lookbook'), 'slider_pagination_style', 'varchar(255) default NULL'
    );
$installer->endSetup();