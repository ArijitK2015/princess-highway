<?php

$installer = $this;
$installer->startSetup();

$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('shippedfrom/shipping_queue'), 'ap_article_id', 'varchar(255) NOT NULL'
    );

$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('shippedfrom/shipping_queue'), 'ap_product_id', 'varchar(255) NOT NULL'
    );

$installer->endSetup();