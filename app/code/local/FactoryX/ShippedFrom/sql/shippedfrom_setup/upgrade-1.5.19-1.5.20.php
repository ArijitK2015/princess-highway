<?php

$installer = $this;
$installer->startSetup();

$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('shippedfrom/orders'), 'order_summary_link', 'varchar(255) NOT NULL'
    );

$installer->endSetup();