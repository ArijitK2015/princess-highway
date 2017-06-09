<?php

$installer = $this;
$installer->startSetup();

// Remove old image table
$installer->run("
DROP TABLE IF EXISTS {$this->getTable('lookbook/image')}
");

$table = $installer->getConnection()
    ->newTable($installer->getTable('lookbook/lookbook_media'))
    ->addColumn('media_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
        ), 'Value ID')
    ->addColumn('lookbook_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned' => true,
        'nullable' => false,
        ), 'Lookbook ID')
    ->addColumn('path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Path')
    ->addColumn('label', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Label')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        ), 'Position')
    ->addColumn('disabled', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned' => true,
        'nullable' => false,
        'default' => '0',
        ), 'Is Disabled')
    ->addColumn('creation_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Media Creation Time')
    ->addColumn('update_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Media Modification Time')
    ->addForeignKey($installer->getFkName('lookbook/lookbook_media', 'lookbook_id', 'lookbook/lookbook', 'lookbook_id'), 'lookbook_id', $installer->getTable('lookbook/lookbook'), 'lookbook_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('FactoryX Lookbook Media');

$installer->getConnection()->createTable($table);

/**
 * Add fields needed for a working media gallery.
 */
$installer->getConnection()
    ->addColumn($installer->getTable('lookbook/lookbook'), FactoryX_Lookbook_Model_Lookbook::BASE_IMAGE, array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => true,
        'default' => FactoryX_Lookbook_Model_Lookbook::NO_SELECTION,
        'comment' => 'Lookbook base image',
        )
    );

$installer->getConnection()
    ->addColumn($installer->getTable('lookbook/lookbook'), FactoryX_Lookbook_Model_Lookbook::BASE_IMAGE . '_label', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => true,
        'comment' => 'Lookbook base image label',
        )
    );

$installer->getConnection()
    ->addColumn($installer->getTable('lookbook/lookbook'), FactoryX_Lookbook_Model_Lookbook::SMALL_IMAGE, array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => true,
        'default' => FactoryX_Lookbook_Model_Lookbook::NO_SELECTION,
        'comment' => 'Lookbook small image',
        )
    );

$installer->getConnection()
    ->addColumn($installer->getTable('lookbook/lookbook'), FactoryX_Lookbook_Model_Lookbook::SMALL_IMAGE . '_label', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => true,
        'comment' => 'Lookbook small image label',
        )
    );

$installer->getConnection()
    ->addColumn($installer->getTable('lookbook/lookbook'), FactoryX_Lookbook_Model_Lookbook::THUMBNAIL, array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => true,
        'default' => FactoryX_Lookbook_Model_Lookbook::NO_SELECTION,
        'comment' => 'Lookbook thumbnail',
        )
    );

$installer->getConnection()
    ->addColumn($installer->getTable('lookbook/lookbook'), FactoryX_Lookbook_Model_Lookbook::THUMBNAIL . '_label', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => true,
        'comment' => 'Lookbook thumbnail label',
        )
    );

$installer->endSetup();