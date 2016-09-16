<?php

$installer = $this;
$installer->startSetup();

// Add a type column to the image table
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('homepage/image'), 'type', 'varchar(255) NOT NULL'
    );

// Add a block_id column to the image table
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('homepage/image'), 'block_id', 'smallint(6) NULL'
    );

$query = sprintf("update %s set type = 'image';",$this->getTable('homepage/image'));

$installer->run($query);

$installer->endSetup();