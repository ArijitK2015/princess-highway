<?php

/**
 * Class FactoryX_BirthdayGift_Block_Adminhtml_System_Config_Form_Button
 */
class FactoryX_BirthdayGift_Block_Adminhtml_System_Config_Form_Button extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /*
     * Set template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('factoryx/birthdaygift/system/config/button.phtml');
    }
 
    /**
     * Return element html
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }
 
    /**
     * Return ajax url for button
     *
     * @return string
     */
    public function getAjaxCheckUrl()
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/birthdaygift/send');
    }
 
    /**
     * Generate button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
            'id'        => 'abandonedcarts_button',
            'label'     => $this->helper('adminhtml')->__('Send'),
            'onclick'   => 'javascript:send(); return false;'
        ));
 
        return $button->toHtml();
    }
}