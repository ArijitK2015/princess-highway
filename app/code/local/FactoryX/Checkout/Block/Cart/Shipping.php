<?php
/**
hide free shipping if any other method is free (price == 0)
*/
class FactoryX_Checkout_Block_Cart_Shipping extends Mage_Checkout_Block_Cart_Shipping
{
    private static $_FREE = 'freeshipping'; // self::$_FREE
    
	public function getEstimateRates() {
		$rates = parent::getQuote()->getShippingAddress()->getShippingRatesCollection();
        //Mage::helper('fx_checkout')->log(sprintf("%s->rates:%s", __METHOD__, count($rates)) );		
		$hideFree = false;
		// Mage_Sales_Model_Quote_Address_Rate		
		foreach($rates as $rate) {
		    if ($rate->getMethod() != self::$_FREE && $rate->getPrice() == 0) {
		        $hideFree = true;
		    }
		}
		$rates = parent::getEstimateRates();
	    if ($hideFree && array_key_exists(self::$_FREE, $rates)) {
	        //Mage::helper('fx_checkout')->log(sprintf("%s->hide:%s", __METHOD__, self::$_FREE) );
		    unset($rates[self::$_FREE]);
	    }
	    //Mage::helper('fx_checkout')->log(sprintf("%s->rates:%s", __METHOD__, print_r($rates, true)) );
		return $rates;
	}
}
?>