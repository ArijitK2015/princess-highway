<?php
/**
 * @package
 * @author FactoryX Developers (raphael@factoryx.com.au)
 */
class FactoryX_Gua_Model_System_Config_Source_Addto
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'head', 'label'=>Mage::helper('factoryx_gua')->__('Head')),
            array('value' => 'before_body_end', 'label'=>Mage::helper('factoryx_gua')->__('Before Body End')),
        );
    }
}