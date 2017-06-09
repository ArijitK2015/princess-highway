<?php
/**
 * Add a method to set the rates
 * FactoryX_Checkout_Block_Cart_Shipping
*/
class FactoryX_Checkout_Block_Cart_Shipping extends Mage_Checkout_Block_Cart_Shipping
{
    public function setEstimateRates($rates)
    {
        $this->_rates = $rates;
    }
}