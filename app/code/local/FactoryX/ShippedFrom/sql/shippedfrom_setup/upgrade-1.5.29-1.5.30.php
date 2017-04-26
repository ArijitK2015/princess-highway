<?php

$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();
$orderTable = $installer->getTable('shippedfrom/orders');

if (!$conn->tableColumnExists($orderTable, 'total_cost')) {
    $conn->addColumn($orderTable, 'total_cost', 'DECIMAL(12,4) null');
}

$installer->endSetup();