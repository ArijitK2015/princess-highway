<?php

$installer = $this;
$installer->startSetup();

// show_look_number
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('lookbook/lookbook'), 'root_template', 'varchar(255) default NULL'
    );


$installer->endSetup();