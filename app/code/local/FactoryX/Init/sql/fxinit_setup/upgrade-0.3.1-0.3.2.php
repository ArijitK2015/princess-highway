<?php
/**
Insert swatch for base_colour
**/

$installer = $this;
$installer->startSetup();

$helper = Mage::helper('fxinit');

$orderby = "ASC";
$attribute = Mage::getSingleton("eav/config")->getAttribute("catalog_product", "colour_base");
$options = Mage::getResourceModel('eav/entity_attribute_option_collection')
    ->setAttributeFilter( $attribute->getId() )
    ->setStoreFilter()
    ->setPositionOrder($orderby);
$options->getSelect()->order(sprintf("main_table.sort_order %s", $orderby));
// $options = $attribute->getSource()->getAllOptions(false);
/*
    [option_id] => 3124
    [attribute_id] => 210
    [sort_order] => 13
    [image] =>
    [additional_image] =>
    [default_value] => yellow
    [store_default_value] => yellow
    [value] => yellow
*/
$resource = Mage::getSingleton('core/resource');
$wConn = $resource->getConnection('core_write');
foreach($options as $option) {
    //Mage::log(sprintf("option=%s", print_r($option, true)) );
    $imagePath = sprintf("images/swatches/%02d_%s.jpg", $option['sort_order'] + 1, $option['value']);
    $sql = sprintf("UPDATE eav_attribute_option SET sort_order = %d, image = '%s', additional_image = '%s' WHERE option_id = %d", 
        $option['sort_order'], $imagePath, $addImage = "", $option['option_id']);
    Mage::log(sprintf("%s->sql=%s", __METHOD__, $sql) );
    $wConn->query($sql);
}

$installer->endSetup();

?>