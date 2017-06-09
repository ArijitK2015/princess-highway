<?php

$installer = $this;
$installer->startSetup();

$setup = new Mage_Catalog_Model_Resource_Setup('core_setup');
$setup->updateAttribute('catalog_product', 'available_date', 'frontend_class', '');
$setup->removeAttribute('catalog_product', 'fx_cons_location');
$setup->removeAttribute('catalog_product', 'pre_order');
$setup->removeAttribute('catalog_product', 'online_only');

$salesSetup = new Mage_Sales_Model_Resource_Setup('core_setup');
$salesSetup->removeAttribute('order_item', 'brand');
$salesSetup->removeAttribute('quote_item', 'brand');
$salesSetup->removeAttribute('order_item', 'season');
$salesSetup->removeAttribute('quote_item', 'season');
$salesSetup->removeAttribute('order_item', 'rrp');
$salesSetup->removeAttribute('quote_item', 'rrp');
$salesSetup->removeAttribute('order_item', 'fx_cons_location');
$salesSetup->removeAttribute('quote_item', 'fx_cons_location');
$salesSetup->removeAttribute('order_item', 'pre_order');
$salesSetup->removeAttribute('quote_item', 'pre_order');
$salesSetup->removeAttribute('order_item', 'online_only');
$salesSetup->removeAttribute('quote_item', 'online_only');

$installer->endSetup();
