<?php
/**
 * Add a method to set the rates
 * FactoryX_Checkout_Block_Onepage_Shipping_Method_Available
 */
class FactoryX_Checkout_Block_Onepage_Shipping_Method_Available extends Mage_Checkout_Block_Onepage_Shipping_Method_Available
{
    public function setShippingRates($rates)
    {
        $this->_rates = $rates;
    }
}