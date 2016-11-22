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
 * Create table 'instagram/instagramimage'
 */

if ($installer->getConnection()->isTableExists($installer->getTable('instagram/old_instagramimage'))) {
    $installer->run("update eav_attribute set source_model = 'instagram/source_instagram' where attribute_code='instagram_source';");
    return;
}

$table = $installer->getConnection()
    ->newTable($installer->getTable('instagram/old_instagramimage'))
    ->addColumn('image_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false,
        'primary'  => true,
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
    ->setComment('iKantam Modules');

$installer->getConnection()->createTable($table);

/**
 * Create product attribute 'instagram_source'
 */
$installer->addAttribute('catalog_product', 'instagram_source', array(
    'type'                       => 'varchar',
    'label'                      => 'Used Instagram Tags',
    'input'                      => 'multiselect',
    'backend'                    => 'eav/entity_attribute_backend_array',
    'required'                   => false,
    'sort_order'                 => 6,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'apply_to'                   => 'simple,configurable,virtual',
    'searchable'        => false,
	'filterable'        => false,
	'source' => 'instagram/source_instagram',
));

$installer->endSetup();
