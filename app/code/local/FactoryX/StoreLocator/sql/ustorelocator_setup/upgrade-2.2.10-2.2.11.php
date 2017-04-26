<?php
$installer = $this;
$installer->startSetup();

$table = $installer->getTable('ustorelocator_location');

$conn = $installer->getConnection();

if (!$conn->tableColumnExists($table, 'label_layout')) {
    $conn->addColumn($table, 'label_layout', 'VARCHAR(6) NULL');
}

if (!$conn->tableColumnExists($table, 'label_branded')) {
    $conn->addColumn($table, 'label_branded', 'TINYINT(1) NULL');
}

if (!$conn->tableColumnExists($table, 'label_left_offset')) {
    $conn->addColumn($table, 'label_left_offset', 'INT(5) NULL');
}

if (!$conn->tableColumnExists($table, 'label_top_offset')) {
    $conn->addColumn($table, 'label_top_offset', 'INT(5) NULL');
}

$installer->endSetup();