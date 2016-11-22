<?php

/**
 * Class FactoryX_CouponValidation_Block_Adminhtml_Couponvalidation_Validation
 */
class FactoryX_CouponValidation_Block_Adminhtml_Couponvalidation_Validation extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'couponvalidation';
        $this->_controller = 'adminhtml_couponvalidation';
        $this->_mode = 'validation';
        $this->_updateButton('save', 'label', Mage::helper('couponvalidation')->__('Validate'));
        $this->_addButton('redeem', array(
            'label'     => Mage::helper('couponvalidation')->__('Redeem'),
            'class'     => 'delete',
            'onclick'   => 'editForm.submit(\'' . $this->getRedeemUrl() . '\');',
        ));
    }

    /**
     * @return mixed
     */
    public function getHeaderText()
    {
        return Mage::helper('couponvalidation')->__("Validation");
    }

    /**
     * Get the redeem URL
     * @return string
     */
    public function getRedeemUrl()
    {
        return $this->getUrl('*/*/redeem');
    }
}