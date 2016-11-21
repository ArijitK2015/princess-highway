<?php

/**
 * Class FactoryX_CampaignMonitor_Block_Adminhtml_System_Config_Form_Field_List
 */
class FactoryX_CampaignMonitor_Block_Adminhtml_System_Config_Form_Field_List extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return mixed
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $listDetails = Mage::helper('campaignmonitor/cm')->getListDetails();
        return parent::_getElementHtml($element) . "List Name: <strong>" . $listDetails->Title . "</strong>";
    }
}