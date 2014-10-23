<?php

$installer = $this;
$installer->startSetup();

// Bundle click through
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('lookbook/lookbook'), 'bundle_click', 'tinyint(1) default NULL'
    );

// Click to new tab?
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('lookbook/lookbook'), 'click_new_tab', 'tinyint(1) default NULL'
    );

$installer->endSetup();