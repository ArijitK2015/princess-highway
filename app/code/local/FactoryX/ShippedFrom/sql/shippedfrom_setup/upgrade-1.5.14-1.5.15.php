<?php

$installer = $this;
$installer->startSetup();

$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('shippedfrom/account_product'), 'associated_account', 'INT(10) NOT NULL'
    );

$installer->endSetup();