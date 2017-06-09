<?php
if(!($this instanceof Mage_Catalog_Model_Resource_Setup)) {
    throw new Exception("Resource Class needs to inherit from Mage_Catalog_Model_Resource_Setup for this to work!");
}

$installer = $this;
$installer->startSetup();

$groupName = 'Clothing Attributes';
    
$preorderPos = 1;
$attribute = array(
    "code"  => Mage::helper('fxcons')->getConsolidatedAttribute($preorderPos),
    "group" => 1,
    "label" => "Pre Order",
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
    Mage::helper('fxcons')->log(sprintf("check for attribute %s", Mage::helper('fxcons')->getConsolidatedAttribute($preorderPos)));

    if (!Mage::helper('fxcons')->productAttributeExists(Mage::helper('fxcons')->getConsolidatedAttribute($preorderPos))) {
        Mage::helper('fxcons')->log(sprintf("create attribute '%s'", Mage::helper('fxcons')->getConsolidatedAttribute($preorderPos)));
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
 * Add 'online_only' attribute for entities
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
    'type'     => Varien_Db_Ddl_Table::TYPE_SMALLINT,
    'visible'  => true,
    'required' => false
);

$updateCurrentOrderItems = true;

$resource = Mage::getSingleton('core/resource');
$readConnection = $resource->getConnection('core_read');

foreach ($entities as $entity => $table) {
    Mage::helper('fxcons')->log(sprintf("adding '%s' to '%s'", Mage::helper('fxcons')->getConsolidatedAttribute($preorderPos), $entity) );
    try {
        $query = sprintf("show columns from %s like '%s'", $table, Mage::helper('fxcons')->getConsolidatedAttribute($preorderPos));
        //Mage::helper('fxcons')->log(sprintf("query=%s", $query));
        $result = $readConnection->fetchAll($query);
        if (count($result) == 0) {
            Mage::helper('fxcons')->log("ok");
            $installer->addAttribute($entity, Mage::helper('fxcons')->getConsolidatedAttribute($preorderPos), $options);
        }
        else {
            if (preg_match("/sales_flat_order_item/", $table)) {
                $updateCurrentOrderItems = false;
            }
            Mage::helper('fxcons')->log(sprintf("field '%s' in table '%s' already exists!", Mage::helper('fxcons')->getConsolidatedAttribute($preorderPos), $table), Zend_Log::WARN);
        }
    }
    // Column already exists: 1060 Duplicate column name 'pre_order'
    catch(Exception $ex) {
        Mage::helper('fxcons')->log(sprintf("Exception: %s", $ex->getMessage()), Zend_Log::ERR);
    }
}

// update any current pre_order quote items (not really required)
/*
$installer->run(sprintf("
    UPDATE sales_flat_quote_item SFQI
    LEFT JOIN catalog_product_entity CPE ON CPE.sku = SFQI.sku
    LEFT JOIN catalog_product_entity_int EPEI ON EPEI.entity_id = CPE.entity_id
    LEFT JOIN eav_attribute EA ON EPEI.attribute_id = EA.attribute_id
    SET SFQI.pre_order = EPEI.value
    WHERE EA.attribute_code = '%s' AND EPEI.store_id = 0 AND EPEI.value = 1
", FactoryX_Consolidated_Helper_Data::$_CONSOLIDATED_ATTRIBUTES));
*/

// update any current pre_order order items
if ($updateCurrentOrderItems) {
    Mage::helper('fxcons')->log("update current order items");
    $installer->run(sprintf("
        UPDATE sales_flat_order_item SFOI
        LEFT JOIN sales_flat_order SFO ON SFO.entity_id = SFOI.order_id
        LEFT JOIN catalog_product_entity CPE ON CPE.sku = SFOI.sku
        LEFT JOIN catalog_product_entity_int EPEI ON EPEI.entity_id = CPE.entity_id
        LEFT JOIN eav_attribute EA ON EPEI.attribute_id = EA.attribute_id
        SET SFOI.pre_order = EPEI.value
        WHERE EA.attribute_code = '%s' AND EPEI.store_id = 0 AND EPEI.value = 1 AND state = 'processing'
    ", Mage::helper('fxcons')->getConsolidatedAttribute($preorderPos)));
}

$installer->endSetup();
