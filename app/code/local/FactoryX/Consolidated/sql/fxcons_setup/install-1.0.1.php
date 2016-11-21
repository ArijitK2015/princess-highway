<?php
/**
use <class>Mage_Catalog_Model_Resource_Setup</class>
*/

if(!($this instanceof Mage_Catalog_Model_Resource_Setup)) {
    throw new Exception("Resource Class needs to inherit from Mage_Catalog_Model_Resource_Setup for this to work!");
}

$installer = $this;
$installer->startSetup();

$onlineOnlyPos = 0;
$groupName = 'Clothing Attributes';
    
$attribute = array(
    "code"  => Mage::helper('fxcons')->getConsolidatedAttribute($onlineOnlyPos),
    "group" => 1,
    "label" => "Online Only",
    "values" => array(
        "frontend_input"        => "boolean",
        "source_model"          => "eav/entity_attribute_source_boolean",
        "backend_type"          => "int",
        "is_global"             => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        "apply_to"              => 'simple,configurable',
        //"is_visible"            => 1,
        "is_required"           => 0,
        "is_configurable"       => 0,
        "is_visible_on_front"   => 0
    )
);

try {
    Mage::helper('fxcons')->log(sprintf("check for attribute %s", Mage::helper('fxcons')->getConsolidatedAttribute($onlineOnlyPos)));

    if (!Mage::helper('fxcons')->productAttributeExists(Mage::helper('fxcons')->getConsolidatedAttribute($onlineOnlyPos))) {
        Mage::helper('fxcons')->log(sprintf("create attribute '%s'", Mage::helper('fxcons')->getConsolidatedAttribute($onlineOnlyPos)));
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

$installer->endSetup();
