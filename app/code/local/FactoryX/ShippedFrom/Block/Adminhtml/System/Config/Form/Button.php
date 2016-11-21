<?php

/**
 * Class FactoryX_ShippedFrom_Block_Adminhtml_System_Config_Form_Button
 */
abstract class FactoryX_ShippedFrom_Block_Adminhtml_System_Config_Form_Button extends Mage_Adminhtml_Block_System_Config_Form_Field {

    /**
     * Return url for button
     *
     * @return string
    */
    abstract public function getRoute();

    /**
     * Generate button html
     *
     * @return string
     */
    //protected function getElementHtml(Varien_Data_Form_Element_Abstract $element) {
    /*
    public function getButtonHtml() {
        // @var $config Varien_Simplexml_Element
        //$config = $this->getFieldConfig();
    }
    */

    /*
     * Set template
     */
    protected function _construct() {
        parent::_construct();
        //$this->setTemplate('factoryx/shippedfrom/system/config/button.phtml');
    }
 
    /**
     * Return element html
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'id'        => $element->getId(),
                'label'     => Mage::helper('adminhtml')->__($element->getLabel()),
                //'onclick'   => 'setLocation('{$this->getRoute()}' )',
                'class'     => 'save' // save | go ...
            )
        );
        $button->setOnClick(sprintf("window.location.href='%s'", $this->getRoute()));
        return $button->toHtml();
    }
     
}