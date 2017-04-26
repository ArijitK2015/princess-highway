<?php

$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();
$orderTable = $installer->getTable('shippedfrom/orders');

if (!$conn->tableColumnExists($orderTable, 'created_at')) {
    $conn->addColumn($orderTable, 'created_at', 'TIMESTAMP null');
}

$installer->endSetup();