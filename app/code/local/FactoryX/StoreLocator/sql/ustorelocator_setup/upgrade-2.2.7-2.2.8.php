<?php
$installer = $this;
$installer->startSetup();

$table = $installer->getTable('ustorelocator_location');

$conn = $installer->getConnection();

if (!$conn->tableColumnExists($table, 'postcode')) {
    $conn->addColumn($table, 'postcode', 'VARCHAR(4) NULL');
}

if (!$conn->tableColumnExists($table, 'suburb')) {
    $conn->addColumn($table, 'suburb', 'VARCHAR(255) NULL');
}

$installer->endSetup();