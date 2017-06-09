<?php

$installer = $this;
$installer->startSetup();

// click_to_url
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('lookbook/lookbook'), 'click_to_url', 'varchar(255) default NULL'
    );

$installer->endSetup();