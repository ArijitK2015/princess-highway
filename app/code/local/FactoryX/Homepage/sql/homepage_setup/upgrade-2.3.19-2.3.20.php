<?php

$installer = $this;
$installer->startSetup();

// Add a slider direction column to the homepage table
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('homepage/homepage'), 'slider_direction', 'varchar(10) default NULL'
    );

$installer->endSetup();