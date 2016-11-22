<?php
/**

select * from core_config_data where path = 'advanced/modules_disable_output/FactoryX_CouponValidation';
*/

class FactoryX_CouponValidation_Block_Adminhtml_System_Config_Form_Field_DisableModulesOutput extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $status = 'enabled';
        if (Mage::getStoreConfig('advanced/modules_disable_output/' . $this->getModuleName())) {
            $status = 'disabled';
        }
        return sprintf("<span style='font-weight:bold; color: %s;'>%s</span>", (preg_match("/disabled/", $status) ? "red" : "green"), $status);
    }
}