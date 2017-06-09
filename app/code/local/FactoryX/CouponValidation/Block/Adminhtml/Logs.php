<?php

/**
 * Class FactoryX_CouponValidation_Block_Adminhtml_Logs
 */
class FactoryX_CouponValidation_Block_Adminhtml_Logs extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_logs';
        $this->_blockGroup = 'couponvalidation';
        $this->_headerText = Mage::helper('couponvalidation')->__('Redeemed Coupon Logs');
        parent::__construct();
    }

}