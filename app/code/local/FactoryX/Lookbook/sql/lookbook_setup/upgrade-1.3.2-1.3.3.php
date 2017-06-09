<?php

$installer = $this;
$installer->startSetup();


// Update the include_in_nav column to handle the different position in the nav bar

$installer
    ->getConnection()
    ->modifyColumn(
        $this->getTable('lookbook/lookbook'), 'include_in_nav', 'varchar(255) default NULL'
    );

// Add a category nav column
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('lookbook/lookbook'), 'nav_category', 'int(11) NOT NULL'
    );