<?php

$installer = $this;
$installer->startSetup();

// show_look_number
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('lookbook/lookbook'), 'show_look_number', 'tinyint(1) default NULL'
    );


$installer->endSetup();