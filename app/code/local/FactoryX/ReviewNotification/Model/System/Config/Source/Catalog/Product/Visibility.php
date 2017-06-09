<?php


class FactoryX_ReviewNotification_Model_System_Config_Source_Catalog_Product_Visibility
{

    public function toOptionArray()
    {
        return Mage::getModel('catalog/product_visibility')->getAllOptions();
    }

}
