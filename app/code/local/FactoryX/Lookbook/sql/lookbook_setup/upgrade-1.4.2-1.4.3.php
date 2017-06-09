<?php

$installer = $this;

$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('lookbook/lookbook'),
        'slides_per_group',
        array(
            'type'     => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'nullable' => false,
            'default'  => 1,
            'comment'  => 'Slide Per Page'
        )
    );

$installer->endSetup();