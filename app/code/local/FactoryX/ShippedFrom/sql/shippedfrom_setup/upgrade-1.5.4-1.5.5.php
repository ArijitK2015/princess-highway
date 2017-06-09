<?php

$installer = $this;
$installer->startSetup();

$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('shippedfrom/orders'), 'ap_payment_id', 'varchar(255)'
    );

$installer->endSetup();