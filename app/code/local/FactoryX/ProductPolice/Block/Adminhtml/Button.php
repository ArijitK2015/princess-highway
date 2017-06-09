<?php

/**
 * Class FactoryX_ProductPolice_Block_Adminhtml_Button
 */
class FactoryX_ProductPolice_Block_Adminhtml_Button extends Mage_Adminhtml_Block_System_Config_Form_Field{
    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return mixed
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $url = Mage::helper("adminhtml")->getUrl("adminhtml/productpolice/dbscan");

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('scalable')
            ->setLabel('Scan')
            ->setOnClick("setLocation('$url')")
            ->toHtml();

        return $html;
    }
}