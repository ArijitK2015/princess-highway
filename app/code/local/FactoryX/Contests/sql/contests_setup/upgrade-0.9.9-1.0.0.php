<?php

$installer = $this;
$installer->startSetup();

$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('contests/contest'), 'states', 'text default NULL'
    );


$installer->endSetup();