<?php

$installer = $this;
$installer->startSetup();

$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('shippedfrom/account'), 'mapping_type', 'VARCHAR(5)'
    );

$installer->endSetup();