<?php

$installer = $this;
$installer->startSetup();

// Add a sourced_from column to the sales_flat_shipment_item table 
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('sales/order_item'), 'sourced_from', 'int(10) unsigned'
    );

$installer->endSetup();