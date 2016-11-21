<?php

$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();
$shipmentTable = $installer->getTable('sales/shipment');
$shipmentGridTable = $installer->getTable('sales/shipment_grid');
$adminUserTable = $installer->getTable('admin/user');
$orderItemTable = $installer->getTable('sales/order_item');

if ($conn->tableColumnExists($orderItemTable, 'sourced_by'))
{
    $conn->dropColumn($orderItemTable, 'sourced_by');
}

if ($conn->tableColumnExists($orderItemTable, 'sourced_from'))
{
    $conn->dropColumn($orderItemTable, 'sourced_from');
}

if ($conn->tableColumnExists($adminUserTable, 'store'))
{
    $conn->dropColumn($adminUserTable, 'store');
}

if ($conn->tableColumnExists($shipmentTable, 'shipped_by'))
{
    $conn->dropColumn($shipmentTable, 'shipped_by');
}

if ($conn->tableColumnExists($shipmentGridTable, 'shipped_by'))
{
    $conn->dropColumn($shipmentGridTable, 'shipped_by');
}

if ($conn->tableColumnExists($shipmentTable, 'shipped_from'))
{
    $conn->dropColumn($shipmentTable, 'shipped_from');
}

if ($conn->tableColumnExists($shipmentGridTable, 'shipped_from'))
{
    $conn->dropColumn($shipmentGridTable, 'shipped_from');
}

$installer->endSetup();
