<?php

$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "coupon");

$usedInForms = ['adminhtml_customer'];

$attribute->setData("used_in_forms", $usedInForms)
    ->setData("is_used_for_customer_segment", true)
    ->setData("is_system", 0)
    ->setData("is_user_defined", 1)
    ->setData("is_visible", 1)
    ->setData("sort_order", 100)
    ->save();

$installer->endSetup();
