<?php

class MDN_EasyReview_Block_System_Config_Button_SendTestEmail extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $url = $this->getUrl('EasyReview/Admin/SendTest');

        $html = $this->__('Order # : ').' <input type="text" name="test_order_id" id="test_order_id">&nbsp;';

        $html .= $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('scalable')
                    ->setLabel('Send !')
                    ->setOnClick("document.location.href = '$url' + 'order_id/' + document.getElementById('test_order_id').value;")
                    ->toHtml();

        return $html;
    }
}