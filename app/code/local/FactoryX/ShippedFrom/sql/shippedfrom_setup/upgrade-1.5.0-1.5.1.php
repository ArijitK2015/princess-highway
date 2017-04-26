<?php

$installer = $this;
$installer->startSetup();

$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('shippedfrom/account'), 'location_id', 'int(10) unsigned NOT NULL'
    );

$installer->endSetup();