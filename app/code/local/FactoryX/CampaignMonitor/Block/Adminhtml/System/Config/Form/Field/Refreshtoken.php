<?php

/**
 * Class FactoryX_CampaignMonitor_Block_Adminhtml_System_Config_Form_Field_Refreshtoken
 */
class FactoryX_CampaignMonitor_Block_Adminhtml_System_Config_Form_Field_Refreshtoken extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    // Template to the button
    protected $_template = "factoryx/campaignmonitor/system/config/form/field/refreshtoken.phtml";

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }
}
