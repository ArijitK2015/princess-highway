<?php

$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$entityTypeId     = $setup->getEntityTypeId('customer');
$attributeSetId   = $setup->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $setup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

// Add an attribute to the customer to ensure no one is cheating with birthday to get unlimited coupons
$setup->addAttribute('customer', 'last_birthday_coupon', array(
	'input'         => 'text',
	'type'          => 'text',
	'label'         => 'Last Birthday Coupon',
	'visible'       => 0,
	'required'      => 0,
	'user_defined'  => 0,
));

$setup->addAttributeToGroup(
	$entityTypeId,
	$attributeSetId,
	$attributeGroupId,
	'last_birthday_coupon',
	'999'
);

$installer->endSetup();
