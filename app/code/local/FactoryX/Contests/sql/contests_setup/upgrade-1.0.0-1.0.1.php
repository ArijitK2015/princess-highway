<?php

$installer = $this;
$installer->startSetup();

$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('contests/contest'), 'gender', 'tinyint(1) default NULL'
    );

$installer->endSetup();