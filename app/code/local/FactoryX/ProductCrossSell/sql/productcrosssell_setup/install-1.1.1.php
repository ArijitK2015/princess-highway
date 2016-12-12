<?php
/**
Mage_Catalog_Model_Resource_Product_Link_Product_Collection does not contain the product sku needed for image mapping

select used_in_product_listing from catalog_eav_attribute where attribute_id = (select attribute_id from eav_attribute where attribute_code = 'sku');
update catalog_eav_attribute set used_in_product_listing = 0 where attribute_id = (select attribute_id from eav_attribute where attribute_code = 'sku');
*/

$installer = $this;

$installer->startSetup();

$installer->run("update catalog_eav_attribute set used_in_product_listing = 1 where attribute_id = (select attribute_id from eav_attribute where attribute_code = 'sku');");

$installer->endSetup();
