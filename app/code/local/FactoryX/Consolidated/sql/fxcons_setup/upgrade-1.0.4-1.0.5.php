<?php

if(!($this instanceof Mage_Catalog_Model_Resource_Setup)) {
    throw new Exception("Resource Class needs to inherit from Mage_Catalog_Model_Resource_Setup for this to work!");
}

$installer = $this;
$installer->startSetup();

$groupName = 'Clothing Attributes';

$attrPos = 2;
$attribute = array(
    "code"  => Mage::helper('fxcons')->getConsolidatedAttribute($attrPos),
    "group" => 1,
    "label" => "Consolidated Item Location",
    "values" => array(
        "frontend_input"        => "text",
        "backend_type"          => "varchar",
        "is_global"             => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        "apply_to"              => 'simple,configurable',
        //"is_visible"          => 1,
        "is_required"           => 0,
        "is_configurable"       => 0,
        "is_visible_on_front"   => 0
    )
);

try {
    Mage::helper('fxcons')->log(sprintf("check for attribute %s", Mage::helper('fxcons')->getConsolidatedAttribute($attrPos)));

    if (!Mage::helper('fxcons')->productAttributeExists(Mage::helper('fxcons')->getConsolidatedAttribute($attrPos))) {
        Mage::helper('fxcons')->log(sprintf("create attribute '%s'", Mage::helper('fxcons')->getConsolidatedAttribute($attrPos)));
        Mage::helper('fxcons')->createAttribute(
            $attribute["label"],
            $attribute["code"],
            $attribute["values"],
            $setInfo = null,
            $options = null,
            $replaceAttribute = false
        );
        Mage::helper('fxcons')->addAttributeToSetGroup(
            $attribute["code"],
            $sets = array(),
            $groupName
        );
    }
}
catch(Exception $ex) {
    Mage::helper('fxcons')->log(sprintf("%s", $ex->getMessage()), Zend_Log::ERR);
}

// Setup Model of Sales Module
$installer = new Mage_Sales_Model_Resource_Setup('core_setup');

/**
 * Add attribute for entities
 */
$entities = array(
    //'quote',
    //'quote_address',
    'quote_item' => 'sales_flat_quote_item',
    //'quote_address_item',
    //'order',
    'order_item' => 'sales_flat_order_item'
);

$options = array(
    'type'     => Varien_Db_Ddl_Table::TYPE_VARCHAR,
    'visible'  => true,
    'required' => false
);

$resource = Mage::getSingleton('core/resource');
$readConnection = $resource->getConnection('core_read');

foreach ($entities as $entity => $table) {
    Mage::helper('fxcons')->log(sprintf("adding '%s' to '%s'", Mage::helper('fxcons')->getConsolidatedAttribute($attrPos), $entity) );
    try {
        $query = sprintf("show columns from %s like '%s'", $table, Mage::helper('fxcons')->getConsolidatedAttribute($attrPos));
        //Mage::helper('fxcons')->log(sprintf("query=%s", $query));
        $result = $readConnection->fetchAll($query);
        if (count($result) == 0) {
            Mage::helper('fxcons')->log("ok");
            $installer->addAttribute($entity, Mage::helper('fxcons')->getConsolidatedAttribute($attrPos), $options);
        }
        else {
            Mage::helper('fxcons')->log(sprintf("field '%s' in table '%s' already exists!", Mage::helper('fxcons')->getConsolidatedAttribute($attrPos), $table), Zend_Log::WARN);
        }
    }
    // Column already exists: 1060 Duplicate column name 'blah'
    catch(Exception $ex) {
        Mage::helper('fxcons')->log(sprintf("Exception: %s", $ex->getMessage()), Zend_Log::ERR);
    }
}

$installer->endSetup();
