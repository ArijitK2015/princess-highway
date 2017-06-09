<?php

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/** @var Mage_Cms_Model_Block $block */
$block = Mage::getModel('cms/block');
$block->setData(
    array(
        'title'         =>  'Cart Message',
        'identifier'    =>  'cart-message',
        'status'        =>  '1',
        'store_id'      =>  '1',
        'content'       =>  '<div class="cart-message text-lowercase"><p>Prices are marked.</p><p>Please note that during SALE periods deliveries will take an extra 3-4 days to dispatch on top of the normal processing and delivery times</p></div>'
    )
);
$block->save();

$installer->endSetup();