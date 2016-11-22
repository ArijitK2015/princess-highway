<?php
/**
 * iKantam
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade InstagramConnect to newer
 * versions in the future.
 *
 * @category    Ikantam
 * @package     FactoryX_Instagram
 * @copyright   Copyright (c) 2012 iKantam LLC (http://www.factoryx.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var $installer Mage_Catalog_Model_Resource_Setup */
$installer  = $this;
$connection = $installer->getConnection();

/**
 * Create table 'instagram/instagramlist'
 */
if (!$installer->getConnection()->isTableExists($installer->getTable('instagram/old_instagramlist')))
{
    $table = $installer->getConnection()
        ->newTable($installer->getTable('instagram/old_instagramlist'))
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
        ), 'Hover');

    $installer->getConnection()->createTable($table);
}

// Add column to existing images
$installer->getConnection()
    ->addColumn($installer->getTable('instagram/old_instagramimage'),
        'list_id',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'nullable' => false,
            'comment' => 'List ID'
        )
    );

// Update the list
$installer->run("
  INSERT INTO {$installer->getTable('instagram/old_instagramlist')} (`list_id`,`updatetype`,`tags`,`users`,`image_size`,`style`,`hover`) VALUES (null,'','','','','','');
  UPDATE {$installer->getTable('instagram/old_instagramlist')} SET updatetype = (SELECT value from core_config_data WHERE path = 'factoryx_instagram/module_options/updatetype');
  UPDATE {$installer->getTable('instagram/old_instagramlist')} SET tags = (SELECT value from core_config_data WHERE path = 'factoryx_instagram/module_options/tags');
  UPDATE {$installer->getTable('instagram/old_instagramlist')} SET users = (SELECT value from core_config_data WHERE path = 'factoryx_instagram/module_options/users');
  UPDATE {$installer->getTable('instagram/old_instagramlist')} SET image_size = (SELECT value from core_config_data WHERE path = 'factoryx_instagram/module_options/image_size');
  UPDATE {$installer->getTable('instagram/old_instagramlist')} SET style = (SELECT value from core_config_data WHERE path = 'factoryx_instagram/module_options/style');
  UPDATE {$installer->getTable('instagram/old_instagramlist')} SET hover = (SELECT value from core_config_data WHERE path = 'factoryx_instagram/module_options/hover');
");

// Delete the values from backend
$installer->run("DELETE from core_config_data WHERE path IN ('factoryx_instagram/module_options/updatetype','factoryx_instagram/module_options/tags','factoryx_instagram/module_options/users','factoryx_instagram/module_options/image_size','factoryx_instagram/module_options/style','factoryx_instagram/module_options/hover')");

$installer->endSetup();
