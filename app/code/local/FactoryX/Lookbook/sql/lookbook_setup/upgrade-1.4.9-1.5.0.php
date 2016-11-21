<?php

$installer = $this;

$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('lookbook/lookbook'),
        'layout',
        array(
            'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => false,
            'comment'  => 'Lookbook Layout'
        )
    );

$installer->endSetup();