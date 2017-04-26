<?php

$installer = $this;
$installer->startSetup();

$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('shippedfrom/shipping_queue'), 'ap_last_url', 'TEXT default NULL'
    );

$installer->endSetup();