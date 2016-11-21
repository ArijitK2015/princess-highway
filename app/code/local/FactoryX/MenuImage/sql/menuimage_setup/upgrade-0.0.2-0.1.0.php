<?php

$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('menuimage/menuimage')} (
  `menuimage_id` int(11) NOT NULL auto_increment,
  `category_id` int(11) NOT NULL default 1,
  `status` tinyint(1) default NULL,
  `added` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `edited` timestamp NOT NULL,
  PRIMARY KEY  (`menuimage_id`)
)  ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS {$this->getTable('menuimage/block')} (
  `block_id` int(11) NOT NULL auto_increment,
  `menuimage_id` int(11) NOT NULL,
  `url` varchar(255) default NULL,
  `link` varchar(255) default NULL,
  `alt` varchar(255) default NULL,
  `product_id` int(11) default NULL,
  `index` int(11) default NULL,
  `sort_order` int(11) default NULL,
  `type` varchar(255) default NULL,
  PRIMARY KEY  (`block_id`),
  FOREIGN KEY  (`menuimage_id`) REFERENCES {$this->getTable('menuimage/menuimage')}(`menuimage_id`)
)  ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS {$this->getTable('menuimage/store')} (
	`menuimage_id` smallint(6) unsigned,
	`store_id` smallint(6) unsigned
)  ENGINE = InnoDB DEFAULT CHARSET = utf8;

");

// Move the old pictures to new system
$categories = Mage::getResourceModel('catalog/category_collection')->addAttributeToSelect('*');
// Count
$i = 1;
foreach($categories as $category)
{
    // Data
    $categoryData = $category->getData();
    $menuImage = $menuImage2 = $menuImage3 = NULL;
    if (array_key_exists('menu_image',$categoryData))
    {
        $menuImage = $categoryData['menu_image'];
    }
    if (array_key_exists('menu_image_2',$categoryData))
    {
        $menuImage2 = $categoryData['menu_image_2'];
    }
    if (array_key_exists('menu_image_3',$categoryData))
    {
        $menuImage3 = $categoryData['menu_image_3'];
    }
    $categoryId = $category->getEntityId();
    $categoryName = $category->getName();
    $edited = Mage::getModel('core/date')->gmtDate();

    if ($menuImage || $menuImage2 || $menuImage3)
    {
        $installer->run("INSERT INTO {$this->getTable('menuimage/menuimage')} (`menuimage_id`,`category_id`,`status`,`added`,`edited`) VALUES ($i, $categoryId, 1, NULL, \"$edited\");");
        if ($menuImage)
        {
            $installer->run("INSERT INTO {$this->getTable('menuimage/block')} (`block_id`,`menuimage_id`,`url`,`link`,`alt`,`product_id`,`index`,`sort_order`,`type`) VALUES (NULL, $i, \"$menuImage\", \"#\", \"$categoryName\", NULL, 1, 1, 'image');");
        }
        if ($menuImage2)
        {
            $installer->run("INSERT INTO {$this->getTable('menuimage/block')} (`block_id`,`menuimage_id`,`url`,`link`,`alt`,`product_id`,`index`,`sort_order`,`type`) VALUES (NULL, $i, \"$menuImage2\", \"#\", \"$categoryName\", NULL, 2, 2, 'image');");
        }
        if ($menuImage3)
        {
            $installer->run("INSERT INTO {$this->getTable('menuimage/block')} (`block_id`,`menuimage_id`,`url`,`link`,`alt`,`product_id`,`index`,`sort_order`,`type`) VALUES (NULL, $i, \"$menuImage3\", \"#\", \"$categoryName\", NULL, 3, 3, 'image');");
        }
        $i++;
    }
}

// Delete the old attributes
$this->removeAttribute('catalog_category','menu_image');
$this->removeAttribute('catalog_category','menu_image_2');
$this->removeAttribute('catalog_category','menu_image_3');

$installer->endSetup();