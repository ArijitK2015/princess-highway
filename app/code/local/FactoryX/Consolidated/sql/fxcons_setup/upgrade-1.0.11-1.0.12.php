<?php

$installer = $this;
$installer->startSetup();

$attribute = "available_date";

$entityTypeId = $installer->getEntityTypeId('catalog_product');
$installer->updateAttribute($entityTypeId, $attribute, 'backend_model', 'fxcons/eav_entity_attribute_backend_availabledate');

$installer->endSetup();
