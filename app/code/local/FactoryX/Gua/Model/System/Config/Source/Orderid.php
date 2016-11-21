<?php
/**
 * @package
 * @author FactoryX Developers (raphael@factoryx.com.au)
 */
class FactoryX_Gua_Model_System_Config_Source_Orderid
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'entity_id', 'label'=>Mage::helper('factoryx_gua')->__('ID')),
            array('value' => 'increment_id', 'label'=>Mage::helper('factoryx_gua')->__('Increment ID')),
        );
    }
}