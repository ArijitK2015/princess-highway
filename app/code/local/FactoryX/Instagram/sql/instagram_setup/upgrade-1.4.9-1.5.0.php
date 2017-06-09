<?php

$installer = $this;

$installer->startSetup();

try {

    if (!$installer->getConnection()->isTableExists($installer->getTable('instagram/instagramlog'))) {
        $table = $installer->getConnection()
            ->newTable($installer->getTable('instagram/instagramlog'))
            ->addColumn('log_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary'  => true
            ), 'Log Id')
            ->addColumn('image_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                'nullable' => false,
            ), 'Image Id')
            ->addColumn('list_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'nullable' => false
            ), 'List ID')
            ->addColumn('added', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
                'nullable' => false,
                'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT
            ), 'Date added')
            ->setComment('Instagram Module');

        $installer->getConnection()->createTable($table);
    }
}
catch(Exception $e)
{
    Mage::logException($e);
}

$installer->endSetup();
