<?php

$installer = $this;
$installer->startSetup();

$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('shippedfrom/orders'), 'order_reference', 'varchar(255) NOT NULL'
    );

$installer->endSetup();