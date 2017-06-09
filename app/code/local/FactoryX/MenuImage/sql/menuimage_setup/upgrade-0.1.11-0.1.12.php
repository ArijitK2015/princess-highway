<?php

$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();
$table = $this->getTable('menuimage/block');

// Alternate state column to have full width

if ($conn->tableColumnExists($table, 'product_id'))
{
    $conn->modifyColumn($table, 'product_id', 'varchar(255) NULL');
}

$installer->endSetup();