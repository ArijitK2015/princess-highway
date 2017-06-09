<?php

/**
 * Class FactoryX_StoreLocator_Block_Adminhtml_System_Config_Form_Button
 */
abstract class FactoryX_StoreLocator_Block_Adminhtml_System_Config_Form_Button extends Mage_Adminhtml_Block_System_Config_Form_Field {

    /**
     * Return url for button
     *
     * @return string
    */
    abstract public function getRoute();

    /*
     * Set template
     */
    protected function _construct() {
        parent::_construct();
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