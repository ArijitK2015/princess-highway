<?php

$installer = $this;

$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('factoryx_notificationboard/notification'),
        'style',
        array(
            'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => true,
            'default'  => null,
            'comment'  => 'Field for CSS style'
        )
    );

$installer->endSetup();