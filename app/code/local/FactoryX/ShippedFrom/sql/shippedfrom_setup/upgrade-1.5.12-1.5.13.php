<?php

$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();
$queueTable = $installer->getTable('shippedfrom/shipping_queue');

if ($conn->tableColumnExists($queueTable, 'created_at')) {
    $conn->modifyColumn($queueTable, 'created_at', 'TIMESTAMP null');
}

$installer->endSetup();