<?php
/**
 * @copyright   Copyright (c) 2010 Amasty
 */
class Amasty_Cheapest_Model_Observer
{
    /**
     * Process "cheapest_for_free" shopping cart rule
     *
     * @param   Varien_Event_Observer $observer
     */
    public function salesrule_validator_process($observer) 
    {
        $rule = $observer->getEvent()->getRule();

        if ($rule->getSimpleAction() != 'cheapest_for_free') {
            return $this;
        }
       
        $item       = $observer->getEvent()->getItem();
        $itemPrice  = $item->getDiscountCalculationPrice();
        if ($itemPrice !== null) {
            $baseItemPrice = $item->getBaseDiscountCalculationPrice();
        } 
        else {
            $itemPrice = $item->getCalculationPrice();
            $baseItemPrice = $item->getBaseCalculationPrice();
        }

        $address = $observer->getEvent()->getAddress();
        $cartRules = $address->getCartFixedRules();
        
        if (!isset($cartRules[$rule->getId()])) {
            /**
             * getCheapestCartItem function returns sum of discount
             * @version 1.2.0
             */
            $cartRules[$rule->getId()] = Mage::helper('amcheapest')->getCheapestCartItem($rule);
        }
    
        $discountAmount     = 0;
        $baseDiscountAmount = 0;
        
        if ($cartRules[$rule->getId()] > 0) {
            
            $quote = $observer->getEvent()->getQuote();
            $quoteAmount = $quote->getStore()->convertPrice($cartRules[$rule->getId()]);
            /**
            * We can't use row total here because row total not include tax
            */
            $qty                = $observer->getEvent()->getQty();
            $discountAmount     = min($itemPrice*$qty - $item->getDiscountAmount(), $quoteAmount);
            $baseDiscountAmount = min($baseItemPrice*$qty - $item->getBaseDiscountAmount(), $cartRules[$rule->getId()]);
            $cartRules[$rule->getId()] -= $baseDiscountAmount;
        }
        
        $address->setCartFixedRules($cartRules);
        
        $result = $observer->getEvent()->getResult();
        $result->setDiscountAmount($discountAmount);
        $result->setBaseDiscountAmount($baseDiscountAmount);

        return $this;
    }
}