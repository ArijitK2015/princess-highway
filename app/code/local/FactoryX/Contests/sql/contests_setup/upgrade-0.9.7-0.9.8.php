<?php

$installer = $this;
$installer->startSetup();

// Add a campaignmonitor_list column to the contests table
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('contests/contest'), 'campaignmonitor_list', 'text default NULL'
    );


$installer->endSetup();