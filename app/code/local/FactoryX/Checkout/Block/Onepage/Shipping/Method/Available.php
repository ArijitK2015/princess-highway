<?php
/**
hide free shipping if any other method is free (price == 0)
*/
class FactoryX_Checkout_Block_Onepage_Shipping_Method_Available extends Mage_Checkout_Block_Onepage_Shipping_Method_Available
{
    private static $_FREE = 'freeshipping'; // self::$_FREE
   
    public function getShippingRates() {
        $groups = parent::getShippingRates();

        //Mage::helper('fx_checkout')->log(sprintf("%s->rates:%s", __METHOD__, count($groups)) );
        $hideFree = false;
        foreach ($groups as $code => $rates) {
            foreach ($rates as $rate) {
                if ($rate->getMethod() != self::$_FREE && $rate->getPrice() == 0) {
                    $hideFree = true;
                }
            }
        }
        if ($hideFree && array_key_exists(self::$_FREE, $groups)) {
            //Mage::helper('fx_checkout')->log(sprintf("%s->hide:%s", __METHOD__, self::$_FREE) );
            unset($groups[self::$_FREE]);
        }
        //Mage::helper('fx_checkout')->log(sprintf("%s->rates:%s", __METHOD__, print_r($rates, true)) );

        return $groups;
    }
}
?>