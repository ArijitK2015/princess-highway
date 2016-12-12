<?php

/**
 * Class FactoryX_ConditionalAgreement_Block_Adminhtml_System_Config_Form_Field_Enable
 */
class FactoryX_ConditionalAgreement_Block_Adminhtml_System_Config_Form_Field_Enable extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    const CHECKOUT_AGREEMENT_FLAG = "checkout/options/enable_agreements";

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return mixed
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $status = Mage::getStoreConfigFlag(self::CHECKOUT_AGREEMENT_FLAG);

        return "<p><a href='" . Mage::helper('adminhtml')->getUrl('adminhtml/system_config/edit/section/checkout') . "'>Checkout > Enable Terms and Conditions</a> is " . ($status ? "enabled" : "disabled") . "</p>" . parent::_getElementHtml($element);
    }
}