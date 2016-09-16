<?php

$installer = $this;
$installer->startSetup();

// Add a over column to the image table
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('homepage/image'), 'over', 'tinyint(1) DEFAULT 0'
    );

$installer->endSetup();