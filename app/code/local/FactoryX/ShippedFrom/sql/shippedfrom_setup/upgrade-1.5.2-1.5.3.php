<?php

$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();
$queueTable = $installer->getTable('shippedfrom/shipping_queue');

if ($conn->tableColumnExists($queueTable, 'ap_order_id')) {
    $conn->modifyColumn($queueTable, 'ap_order_id', 'varchar(255) NOT NULL');
}

$installer->endSetup();