<?php

/**
 * Class FactoryX_CheckoutDiscount_Block_Checkout_Onepage_Discount
 */
class FactoryX_CheckoutDiscount_Block_Checkout_Onepage_Discount extends Mage_Checkout_Block_Onepage_Abstract
{
    protected function _construct()
    {
        $this->getCheckout()->setStepData('discount', array(
            'label'     => $this->__('Discount Code'),
            'is_show'   => $this->isShow()
        ));
        parent::_construct();
    }

    /**
     * @return mixed
     */
    public function isShow(){
     	return Mage::getStoreConfig('checkoutdiscount/config/enable');
     }
}