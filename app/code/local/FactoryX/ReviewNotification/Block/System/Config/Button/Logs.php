<?php

class FactoryX_ReviewNotification_Block_System_Config_Button_Logs extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $url = $this->getUrl('adminhtml/reviewnotification/logs');

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('scalable')
                    ->setLabel('Display logs')
                    ->setOnClick("setLocation('$url')")
                    ->toHtml();

        return $html;
    }
}