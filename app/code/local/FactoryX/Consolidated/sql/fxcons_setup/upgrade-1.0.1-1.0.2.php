<?php

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
    $onlineOnlyPos = 0;
    Mage::helper('fxcons')->log(sprintf("adding '%s' to '%s'", Mage::helper('fxcons')->getConsolidatedAttribute($onlineOnlyPos), $entity) );
    try {
        $query = sprintf("show columns from %s like '%s'", $table,  Mage::helper('fxcons')->getConsolidatedAttribute($onlineOnlyPos) );
        //Mage::helper('fxcons')->log(sprintf("query=%s", $query));
        $result = $readConnection->fetchAll($query);
        if (count($result) == 0) {
            Mage::helper('fxcons')->log("ok");
            $installer->addAttribute($entity,  Mage::helper('fxcons')->getConsolidatedAttribute($onlineOnlyPos), $options);
        }
        else {
            if (preg_match("/sales_flat_order_item/", $table)) {
                $updateCurrentOrderItems = false;
            }
            Mage::helper('fxcons')->log(sprintf("field '%s' in table '%s' already exists!",  Mage::helper('fxcons')->getConsolidatedAttribute($onlineOnlyPos), $table), Zend_Log::WARN);
        }
    }
    // Column already exists: 1060 Duplicate column name 'online_only'
    catch(Exception $ex) {
        Mage::helper('fxcons')->log(sprintf("Exception: %s", $ex->getMessage()), Zend_Log::ERR);
    }
}

// update any current online_only quote items (not really required)
/*
$installer->run(sprintf("
    UPDATE sales_flat_quote_item SFQI
    LEFT JOIN catalog_product_entity CPE ON CPE.sku = SFQI.sku
    LEFT JOIN catalog_product_entity_int EPEI ON EPEI.entity_id = CPE.entity_id
    LEFT JOIN eav_attribute EA ON EPEI.attribute_id = EA.attribute_id
    SET SFQI.online_only = EPEI.value
    WHERE EA.attribute_code = '%s' AND EPEI.store_id = 0 AND EPEI.value = 1
", FactoryX_Consolidated_Helper_Data::$_CONSOLIDATED_ATTRIBUTES));
*/

// update any current online_only order items
if ($updateCurrentOrderItems) {
    Mage::helper('fxcons')->log("update current order items");
    $installer->run(sprintf("
        UPDATE sales_flat_order_item SFOI
        LEFT JOIN sales_flat_order SFO ON SFO.entity_id = SFOI.order_id
        LEFT JOIN catalog_product_entity CPE ON CPE.sku = SFOI.sku
        LEFT JOIN catalog_product_entity_int EPEI ON EPEI.entity_id = CPE.entity_id
        LEFT JOIN eav_attribute EA ON EPEI.attribute_id = EA.attribute_id
        SET SFOI.online_only = EPEI.value
        WHERE EA.attribute_code = '%s' AND EPEI.store_id = 0 AND EPEI.value = 1 AND state = 'processing'
    ",  Mage::helper('fxcons')->getConsolidatedAttribute($onlineOnlyPos)));
}

$installer->endSetup();
