<?php

class FactoryX_CacheSupport_Block_Adminhtml_Button extends Mage_Adminhtml_Block_System_Config_Form_Field{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $url = Mage::helper("adminhtml")->getUrl("adminhtml/cachesupport/warm");

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('scalable')
            ->setLabel('Warm Cache')
            ->setOnClick("window.open('$url')")
            ->toHtml();

        return $html;
    }
}