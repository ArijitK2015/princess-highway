<?php
/**
 * FactoryX_StoreLocator extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FactoryX
 * @package    FactoryX_StoreLocator
 * @copyright
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   FactoryX
 * @package    FactoryX_StoreLocator
 * @author
 */

$this->startSetup();

$this->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('ustorelocator_location')} (
  `location_id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `region` varchar(20) NOT NULL,
  `latitude` decimal(15,10) NOT NULL,
  `longitude` decimal(15,10) NOT NULL,
  `address_display` text NOT NULL,
  `notes` text NOT NULL,
  `website_url` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `product_types` varchar(255) NOT NULL,
  PRIMARY KEY  (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
");

$table = $this->getTable('ustorelocator_location');
$conn = $this->getConnection();
$conn->addColumn($table, 'country', 'VARCHAR( 100 ) NULL');
$conn->addColumn($table, 'stores', 'VARCHAR( 100 ) NULL');
$conn->addColumn($table, 'icon', 'VARCHAR( 255 ) NULL');
$conn->addColumn($table, 'use_label', 'TINYINT ( 1 ) default 1 NOT NULL');
$conn->addColumn($table, 'is_featured', 'TINYINT ( 1 ) default 0 NOT NULL');
$conn->addColumn($table, 'zoom', 'TINYINT ( 2 ) default 10 NOT NULL');

$table = $this->getTable('ustorelocator_location');
$conn->dropColumn($table, 'product_types');
$conn->addColumn($table, 'store_code', 'VARCHAR( 3 ) AFTER phone');
$conn->addColumn($table, 'store_type', 'VARCHAR( 20 ) default \'standalone\' NOT NULL AFTER store_code');

$table = $this->getTable('admin_user');
$conn->addColumn($table, 'location_id', 'INT( 10 )');
$conn->addForeignKey(
    $this->getFkName('admin_user','location_id','ustorelocator_location','location_id'),
    $this->getTable('admin_user'),
    'location_id',
    $this->getTable('ustorelocator_location'),
    'location_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_CASCADE
);

$table = $this->getTable('ustorelocator_location');
$conn->addColumn($table, 'image', 'varchar(255) default NULL');

$this->endSetup();