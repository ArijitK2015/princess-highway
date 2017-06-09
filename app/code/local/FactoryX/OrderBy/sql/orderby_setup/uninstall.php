<?php

$installer = $this;
$installer->startSetup();

$setup = Mage::getResourceModel('sales/setup','sales_setup');
$setup->removeAttribute('order', 'created_by');

$installer->endSetup();
