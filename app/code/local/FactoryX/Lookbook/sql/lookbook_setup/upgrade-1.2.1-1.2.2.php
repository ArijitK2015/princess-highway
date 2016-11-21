<?php

$installer = $this;
$installer->startSetup();

// Add a facebook option column to the lookbook table
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('lookbook/lookbook'), 'lookbook_facebook', 'tinyint(1) default NULL'
    );

// Add a extra details column to the lookbook table
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('lookbook/lookbook'), 'under_product_info', 'tinyint(1) default NULL'
    );

$installer->endSetup();