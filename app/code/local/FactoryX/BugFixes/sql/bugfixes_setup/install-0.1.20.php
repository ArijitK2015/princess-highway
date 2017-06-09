<?php

$installer = $this;
$installer->startSetup();

Mage::getModel('sales/order')->getResource()->updateGridRecords(
    Mage::getResourceModel('sales/order_collection')->getAllIds()
);

$installer->endSetup();
