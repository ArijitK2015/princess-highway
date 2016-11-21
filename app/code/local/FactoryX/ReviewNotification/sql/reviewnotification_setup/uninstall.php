<?php

$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();
$orderTable = $installer->getTable('sales/order');

if ($conn->tableColumnExists($orderTable, 'easyreview_hashcode'))
{
    $conn->dropColumn($orderTable, 'easyreview_hashcode');
}

if ($conn->tableColumnExists($orderTable, 'easyreview_notified'))
{
    $conn->dropColumn($orderTable, 'easyreview_notified');
}

if ($conn->tableColumnExists($orderTable, 'easyreview_date'))
{
    $conn->dropColumn($orderTable, 'easyreview_date');
}

$installer->endSetup();
