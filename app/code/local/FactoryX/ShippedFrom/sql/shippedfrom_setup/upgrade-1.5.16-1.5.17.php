<?php

$installer = $this;
$installer->startSetup();

$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('shippedfrom/account'), 'state', 'VARCHAR(255)'
    );

$installer->endSetup();