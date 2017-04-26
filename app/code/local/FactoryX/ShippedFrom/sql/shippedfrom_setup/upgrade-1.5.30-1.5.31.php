<?php

$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();
$orderTable = $installer->getTable('shippedfrom/orders');

if (!$conn->tableColumnExists($orderTable, 'number_of_shipments')) {
    $conn->addColumn($orderTable, 'number_of_shipments', 'int(10) null');
}

$installer->endSetup();