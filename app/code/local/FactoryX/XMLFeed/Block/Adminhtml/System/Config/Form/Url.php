<?php
class FactoryX_XMLFeed_Block_Adminhtml_System_Config_Form_Url extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /*
     * Set template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('factoryx/system/config/url.phtml');
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
    public function getXMLFeedUrl()
    {
        //return Mage::helper('adminhtml')->getUrl('adminhtml/adminhtml_atwixtweaks/check');
    }
 
    /**
     * Generate button html
     *
     * @return string
     */
    public function getUrlHtml()
    {
        return $button->toHtml();
    }
}