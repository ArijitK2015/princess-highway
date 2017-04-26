<?php

$installer = $this;
$installer->startSetup();

$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('shippedfrom/shipping_queue'), 'json_request', 'TEXT default NULL'
    );

$installer->endSetup();