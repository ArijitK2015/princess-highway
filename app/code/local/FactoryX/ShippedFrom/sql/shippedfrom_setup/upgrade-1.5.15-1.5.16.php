<?php

$installer = $this;
$installer->startSetup();

$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('shippedfrom/account_product'), 'option_signature_on_delivery_option', 'TINYINT(4) DEFAULT 0 NOT NULL'
    );

$installer
    ->getConnection()    
    ->addColumn(
        $this->getTable('shippedfrom/account_product'), 'option_authority_to_leave_option', 'TINYINT(4) DEFAULT 0 NOT NULL'
    );

$installer
    ->getConnection()    
    ->addColumn(
        $this->getTable('shippedfrom/account_product'), 'option_dangerous_goods_allowed', 'TINYINT(4) DEFAULT 0 NOT NULL'
    );

$installer->endSetup();