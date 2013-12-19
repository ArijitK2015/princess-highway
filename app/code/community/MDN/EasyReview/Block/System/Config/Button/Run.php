<?php

class MDN_EasyReview_Block_System_Config_Button_Run extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $url = $this->getUrl('EasyReview/Admin/Run');

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('scalable')
                    ->setLabel('Run now')
                    ->setOnClick("setLocation('$url')")
                    ->toHtml();

        return $html;
    }
}