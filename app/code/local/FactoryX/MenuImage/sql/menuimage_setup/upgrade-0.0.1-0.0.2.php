<?php
$this->startSetup();

$this->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'menu_image_2', array(
    'group'         => 'Menu Images',
    'input'         => 'image',
    'type'          => 'varchar',
    'label'         => 'Category Menu Image 2',
    'backend'   	=> 'catalog/category_attribute_backend_image',
    'sort_order'    => 100,
    'required'      => false,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$this->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'menu_image_3', array(
    'group'         => 'Menu Images',
    'input'         => 'image',
    'type'          => 'varchar',
    'label'         => 'Category Menu Image 3',
    'backend'   	=> 'catalog/category_attribute_backend_image',
    'sort_order'    => 100,
    'required'      => false,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$this->endSetup();