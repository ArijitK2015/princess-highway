<?php

$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();
$quoteTable = $installer->getTable('sales/quote');


if (Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('abandonedcarts/log')) ) {
    $installer
        ->getConnection()
        ->dropTable($this->getTable('abandonedcarts/log'));
}

if ($conn->tableColumnExists($quoteTable, 'abandoned_notified'))
{
    $conn->dropColumn($quoteTable, 'abandoned_notified');
}

if ($conn->tableColumnExists($quoteTable, 'abandoned_sale_notified'))
{
    $conn->dropColumn($quoteTable, 'abandoned_sale_notified');
}

$installer->endSetup();
