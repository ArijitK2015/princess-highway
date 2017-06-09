<?php

$installer = $this;

$installer->startSetup();

$table = $this->getTable('couponvalidation/log');

$conn = $this->getConnection();

if (!$conn->tableColumnExists($table, 'ip_address')) {
    $conn->addColumn($table, 'ip_address', 'VARCHAR(15) NULL');
}

if (!$conn->tableColumnExists($table, 'store_code')) {
    $conn->addColumn($table, 'store_code', 'VARCHAR(15) NULL');
}

$installer->endSetup();
