<?php

$installer = $this;
$installer->startSetup();

$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('shippedfrom/account_product'), 'contract_valid_from', 'timestamp'
    );

$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('shippedfrom/account_product'), 'contract_valid_to', 'timestamp'
    );

$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('shippedfrom/account_product'), 'contract_expired', 'TINYINT(4) DEFAULT 0 NOT NULL'
    );

$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('shippedfrom/account_product'), 'contract_volumetric_pricing', 'TINYINT(4) DEFAULT 0 NOT NULL'
    );

$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('shippedfrom/account_product'), 'contract_cubing_factor', 'VARCHAR(255)'
    );

$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('shippedfrom/account_product'), 'contract_max_item_count', 'VARCHAR(255)'
    );

$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('shippedfrom/account_product'), 'authority_to_leave_threshold', 'VARCHAR(255)'
    );

$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('shippedfrom/account_product'), 'credit_blocked', 'VARCHAR(255)'
    );

$installer->endSetup();