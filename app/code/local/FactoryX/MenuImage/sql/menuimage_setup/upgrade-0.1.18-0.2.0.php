<?php

$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();
$table = $this->getTable('menuimage/menuimage');

// Alternate state column to have full width

if ($conn->tableColumnExists($table, 'category_id'))
{
    $conn->modifyColumn($table, 'category_id', 'varchar(255) NULL');
}

$installer->endSetup();