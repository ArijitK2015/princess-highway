<?php
/**

*/

class FactoryX_CouponValidation_Block_Adminhtml_System_Config_Form_Field_FrontEnd extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $url = Mage::getUrl('coupon/validation/index', array(
            '_store' => 1
        ));
        if (Mage::helper('couponvalidation')->hashValidationEnabled()) {
            $hash = md5(date('Y-m-d'));
            $url .= sprintf("?hash=%s", $hash);
        }
        return sprintf("<a href='%s' target='_blank'>%s</a>", $url, $url);
    }
}