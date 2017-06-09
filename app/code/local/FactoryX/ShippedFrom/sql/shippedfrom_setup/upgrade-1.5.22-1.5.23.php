<?php

$installer = $this;
$installer->startSetup();

$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('shippedfrom/orders'), 'status', 'varchar(255) NOT NULL'
    );

$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('shippedfrom/orders'), 'merchant_location_id', 'varchar(255) NOT NULL'
    );

$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('shippedfrom/orders'), 'charge_account', 'varchar(255) NOT NULL'
    );

$installer->endSetup();