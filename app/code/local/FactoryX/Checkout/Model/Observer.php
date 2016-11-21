<?php

class FactoryX_Checkout_Model_Observer
{
    private static $_FREE = 'freeshipping';

    /**
     * Hide free shipping if any other method is free (price == 0)
     * @param Varien_Event_Observer $observer
     */
    public function blockCreateAfter(Varien_Event_Observer $observer)
    {
        // Get the block
        $block = $observer->getBlock();

        // Test the block
        if ($block instanceof Mage_Checkout_Block_Cart_Shipping)
        {
            // Get the rates for the shipping address
            $rates = $block->getQuote()->getShippingAddress()->getShippingRatesCollection();
            $hideFree = false;
            // Loop through the rates
            /** $rates Mage_Sales_Model_Quote_Address_Rate */
            foreach($rates as $rate)
            {
                // Check if the rate is not using freeshipping and if the rate is free
                if ($rate->getMethod() != self::$_FREE && $rate->getPrice() == 0)
                {
                    // Flag it
                    $hideFree = true;
                    break;
                }
            }
            // Get the OG block rates
            $rates = $block->getEstimateRates();

            // Check if flagged
            if ($hideFree && array_key_exists(self::$_FREE, $rates))
            {
                // Unset the freeshipping
                unset($rates[self::$_FREE]);
            }
            // Set the new rates
            $block->setEstimateRates($rates);
        }
        elseif ($block instanceof Mage_Checkout_Block_Onepage_Shipping_Method_Available)
        {
            // Get the OG block rates
            $groups = $block->getShippingRates();
            $hideFree = false;
            // Loop through the rates
            foreach ($groups as $code => $rates)
            {
                foreach ($rates as $rate)
                {
                    // Check if the rate is not using freeshipping and if the rate is free
                    if ($rate->getMethod() != self::$_FREE && $rate->getPrice() == 0)
                    {
                        // Flag it
                        $hideFree = true;
                        break;
                    }
                }
            }
            // Check if flagged
            if ($hideFree && array_key_exists(self::$_FREE, $groups))
            {
                // Unset the freeshipping
                unset($groups[self::$_FREE]);
            }
            // Set the new rates
            $block->setShippingRates($groups);
        }
    }
}