<?php

$installer = $this;
$installer->startSetup();

// nav type
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('lookbook/lookbook'), 'nav_type', 'varchar(255) default NULL'
    );

$installer->endSetup();