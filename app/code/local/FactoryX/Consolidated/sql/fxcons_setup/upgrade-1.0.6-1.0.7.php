<?php

if(!($this instanceof Mage_Catalog_Model_Resource_Setup)) {
    throw new Exception("Resource Class needs to inherit from Mage_Catalog_Model_Resource_Setup for this to work!");
}

$installer = new Mage_Sales_Model_Resource_Setup('core_setup');
$installer->startSetup();

/**
 * Add attribute for entities
 */
$entities = array(
    'quote_item' => 'sales_flat_quote_item',
    'order_item' => 'sales_flat_order_item'
);

$options = array(
    'type'     => Varien_Db_Ddl_Table::TYPE_VARCHAR,
    'visible'  => true,
    'required' => false
);

$attrPositions = array(4,5,6);

$resource = Mage::getSingleton('core/resource');
$readConnection = $resource->getConnection('core_read');

foreach ($entities as $entity => $table) {
    foreach ($attrPositions as $attrPos)
    {
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
}

$installer->endSetup();
