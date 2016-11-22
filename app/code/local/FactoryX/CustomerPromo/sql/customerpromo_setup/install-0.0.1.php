<?php

$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$entityTypeId     = $setup->getEntityTypeId('customer');
$attributeSetId   = $setup->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $setup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

// Add a coupon attribute to the customer
$setup->addAttribute('customer', 'coupon', [
    'type'                      => 'text',
    'label'                     => 'Coupon',
    'visible'                   => 1,
    'required'                  => 0,
    'user_defined'              => 0,
]);

$setup->addAttributeToGroup(
    $entityTypeId,
    $attributeSetId,
    $attributeGroupId,
    'coupon',
    '999'
);

$installer->endSetup();
