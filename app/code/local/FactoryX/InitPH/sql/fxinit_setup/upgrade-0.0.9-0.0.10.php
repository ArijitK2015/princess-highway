<?php
/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

if ($installer->getConnection()->isTableExists($installer->getTable('admin/permission_block')) == true) {
    // Whitelist ajax cart pro blocks
    $installer->getConnection()->insertMultiple(
        $installer->getTable('admin/permission_block'),
        array(
            array('block_name' => 'ajaxcartpro/confirmation_items_productimage', 'is_allowed' => 1)
        )
    );
}

// System Configuration update
$envConfig = array(
    "default" => array(
        "ajaxcartpro/addproductconfirmation/content" => '<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 text-uppercase aw-addto-heading">Item Successfully Added<span class="pull-right"><a onclick=\'AW_AjaxCartProUI.hideBlock($("ajaxcartpro-add-confirm"));AW_AjaxCartProUI.hideBlock($("acp-overlay"));\'><i class="fa fa-times fa-lg"></i></a></span></div><div class="col-md-6 col-lg-6 col-xs-12 col-sm-6">{{block type="ajaxcartpro/confirmation_items_productimage" product="$product" resize="200"}}</div><div class="col-md-6 col-lg-6 col-xs-12 col-sm-6 text-left"><h4>{{var product.name}}</h4><p>Item Code: {{var product.sku}}</p>{{block type="ajaxcartpro/confirmation_items_gotocheckout"}}<br />{{block type="ajaxcartpro/confirmation_items_continue"}}</div>'
    )
);
/** @var Mage_Core_Model_Config $coreConfig */
$coreConfig = Mage::getModel('core/config');
foreach ($envConfig["default"] as $path => $val) {
    $coreConfig->saveConfig($path, $val, 'default', 0);
}

$installer->endSetup();