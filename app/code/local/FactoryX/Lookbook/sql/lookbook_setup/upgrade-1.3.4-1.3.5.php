<?php

$installer = $this;
$installer->startSetup();

// show_look_number
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('lookbook/lookbook'), 'page_prompt', 'tinyint(1) default NULL'
    );

$installer->endSetup();