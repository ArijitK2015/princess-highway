<?php

/**
 * Class FactoryX_ShippedFrom_Model_System_Config_Source_FreeShippingProduct
 */
class FactoryX_ShippedFrom_Model_System_Config_Source_FreeShippingProduct
    extends Varien_Object
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            'E34'  =>  Mage::helper('shippedfrom')->__('Regular Post'),
            'T28'  =>  Mage::helper('shippedfrom')->__('Express Post')
        );
    }
}
