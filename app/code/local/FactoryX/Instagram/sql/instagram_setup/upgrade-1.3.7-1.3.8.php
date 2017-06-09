<?php

$installer = $this;

$installer->startSetup();

try {

    if (!$installer->getConnection()->isTableExists($installer->getTable('instagram/instagramimage'))) {
        $table = $installer->getConnection()
            ->newTable($installer->getTable('instagram/instagramimage'))
            ->addColumn('image_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                'nullable' => false,
                'primary' => true,
            ), 'Image Id')
            ->addColumn('username', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                'nullable' => false,
            ), 'Username')
            ->addColumn('caption_text', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                'nullable' => false,
            ), 'Caption Text')
            ->addColumn('standard_resolution_url', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                'nullable' => false,
            ), 'Standard Resolution Url')
            ->addColumn('low_resolution_url', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                'nullable' => false,
            ), 'Low Resolution Url')
            ->addColumn('thumbnail_url', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                'nullable' => false,
            ), 'Thumbnail url')
            ->addColumn('tag', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                'nullable' => false,
            ), 'Tag Value')
            ->addColumn('is_approved', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
                'nullable' => false,
                'default' => 0,
            ), 'Is Approved')
            ->addColumn('is_visible', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
                'nullable' => false,
                'default' => 1,
            ), 'Is Visible on Backend and Frontend')
            ->addColumn('image_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'nullable' => true,
                'default' => 0
            ), 'Image position')
            ->addColumn('link', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                'nullable' => true,
                'default' => ''
            ), 'Image URL')
            ->addColumn('list_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                    'nullable' => false
            ), 'List ID')
            ->setComment('Instagram Module');

        $installer->getConnection()->createTable($table);
    }

    if (!$installer->getConnection()->isTableExists($installer->getTable('instagram/instagramlist')))
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable('instagram/instagramlist'))
            ->addColumn('list_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary'  => true,
            ), 'List Id')
            ->addColumn('updatetype', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                'nullable' => false,
            ), 'Update Type')
            ->addColumn('tags', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                'nullable' => false,
            ), 'Instagram Hashtags')
            ->addColumn('users', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                'nullable' => false,
            ), 'Instagram Users')
            ->addColumn('image_size', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                'nullable' => false,
            ), 'Image Size')
            ->addColumn('style', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                'nullable' => false,
            ), 'Style')
            ->addColumn('hover', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                'nullable' => false,
            ), 'Hover')
            ->addColumn('limit', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                    'nullable'  => true,
                    'default'   => ''
            ), 'List Limit')
            ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                    'nullable'  => true,
                    'default'   => ''
            ), 'List Title')
            ->addColumn('link', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                    'nullable'  => true,
                    'default'   => ''
            ), 'List Link')
            ->addColumn('show_per_page', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                    'nullable'  => true,
                    'default'   => ''
            ), 'List Per Page Limit');

        $installer->getConnection()->createTable($table);
    }

    $installer->run("
      INSERT INTO {$this->getTable('instagram/instagramimage')} SELECT * FROM {$this->getTable('instagram/old_instagramimage')};

      DROP TABLE IF EXISTS {$this->getTable('instagram/old_instagramimage')};

      INSERT INTO {$this->getTable('instagram/instagramlist')} SELECT * FROM {$this->getTable('instagram/old_instagramlist')};

      DROP TABLE IF EXISTS {$this->getTable('instagram/old_instagramlist')};
    ");

}
catch(Exception $e)
{
    Mage::logException($e);
}

$installer->endSetup();
