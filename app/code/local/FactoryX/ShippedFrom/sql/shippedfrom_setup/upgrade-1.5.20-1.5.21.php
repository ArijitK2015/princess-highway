<?php

$installer = $this;
$installer->startSetup();

$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('shippedfrom/shipping_queue'), 'local_label_link', 'varchar(255) NOT NULL'
    );

$installer->endSetup();