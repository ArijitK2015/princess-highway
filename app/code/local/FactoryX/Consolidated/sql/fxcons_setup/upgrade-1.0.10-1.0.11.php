<?php

$installer = $this;
$installer->startSetup();

$attribute = "available_date";

$entityTypeId = $installer->getEntityTypeId('catalog_product');
$installer->updateAttribute($entityTypeId, $attribute, 'frontend_class', 'validate-future-date');

$installer->endSetup();
