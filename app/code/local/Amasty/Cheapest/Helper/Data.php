<?php
/**
 * @copyright   Copyright (c) 2010 Amasty
 */

class Amasty_Cheapest_Helper_Data extends Mage_Core_Helper_Abstract {
    
    /**
     * Retrieve cheapest item in cart
     *
     * @return Mage_Sales_Model_Quote_Item_Abstract
     */
    public function getCheapestCartItem($rule) 
    {
        $cart = Mage::getSingleton('checkout/cart');

        $minimalPrice = 0;

        /**
         * Change mechanism of calculating cheapest items
         * For now we use "Discount Qty Step (Buy X)" field to add more than 1 free item,
         * and we use "Discount amount" to limit count of free items
         *
         * @version 1.2.0
         */
        $discountStep   = (int) $rule->getDiscountStep();
        $maxDiscountQty = (int) $rule->getDiscountAmount();
        $cartQty        = (int) $cart->getItemsQty();

        // if discountStep is not specified - max free items qty equals to 1
        // if discountStep is specified, but discountQty is not - add as many free items as possible (no more than cart items qty)
        if (!$discountStep) {
            $discountQty = 1;
        } else {
            if (!$maxDiscountQty) {
                $maxDiscountQty = $cartQty;
            }

            $discountQty = floor($cartQty / $discountStep);
            
            if ($discountQty > $maxDiscountQty) {
                $discountQty = $maxDiscountQty;
            }
        }

        $cartPrices = array();


        foreach ($cart->getItems() as $item) {
            if (!$rule->getActions()->validate($item)) {
                continue;
            }

            $item->setIsCheapestCartItem(false);
            
            // for bundle items - do not process child products
            if ($item->getParentItemId()) {
                continue;
                
            }

            /**
             * changed in version 1.0.3
             */
            $currentPrice  = $item->getDiscountCalculationPrice();
            if ($currentPrice !== null) {
                $baseItemPrice = $item->getBaseDiscountCalculationPrice();
            } else {
                $currentPrice = $item->getCalculationPrice();
                $baseItemPrice = $item->getBaseCalculationPrice();
            }
            
            /**
             * Make a list of all prices in the cart
             *
             * @version 1.2.0
             */
            for ($itemQty = 0; $itemQty < $item->getQty(); $itemQty++) {
                $cartPrices[] = array(
                    'item' => $item,
                    'price' => $baseItemPrice
                );
            }
        }

        /**
         * Sort the list of prices to detect cheapest of them
         *
         * @version 1.2.0
         */
        usort($cartPrices, array($this, 'sortPrices'));

        $cheapestItems = array_slice($cartPrices, 0, $discountQty);
        
        $discountSum = 0;
        
        foreach($cheapestItems as $cheapestItem) {
            $discountSum += $cheapestItem['price'];
            $cheapestItem['item']->setIsCheapestCartItem(true);
        }

        return $discountSum;
    }

    /**
     * Retrieve cheapest item label
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @return string
     */
    public function getCheapestCartItemLabel($item) {
        $html = '';
        if ($item->getIsCheapestCartItem()) {
            $html = '<br>' . $this->__('Free Item');
        }
        return $html;
    }

    /**
     * Sort prices
     *
     * @static
     * @param  $item1
     * @param  $item2
     * @return void
     */
    static public function sortPrices($item1, $item2)
    {
        if ($item1['price'] == $item2['price']) {
            return 0;
        }
        return ($item1['price'] < $item2['price']) ? -1 : 1;
    }
}
