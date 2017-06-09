<?php

/**
 * Class FactoryX_PromoRestriction_Model_Observer
 */
class FactoryX_PromoRestriction_Model_Observer
{

    /**
     * @param Varien_Event_Observer $observer
     */
    public function validate(Varien_Event_Observer $observer)
    {
        try {
            $this->validateRestriction($observer);
        }
        catch (Mage_Core_Exception $e) {
            Mage::getSingleton('checkout/session')->addError($e->getMessage());
        }
        catch (Exception $e) {
            Mage::getSingleton('checkout/session')->addError(Mage::helper('promorestriction')->__('Cannot apply the coupon code.'));
            Mage::logException($e);
        }
    }


    /**
     * @param Varien_Event_Observer $observer
     */
    public function validateRestriction(Varien_Event_Observer $observer)
    {
        $errMsg = false;
        // Get the rule id
        $ruleId = $observer->getEvent()->getRule()->getRuleId();

        // Load possible restriction
        $restriction = Mage::getModel('promorestriction/restriction')->load($ruleId, 'salesrule_id');

        // Validate only if there is a restriction
        if ($restrictedField = $restriction->getRestrictedField()) {
            //Mage::helper('promorestriction')->log(sprintf("%s->restricted: %s|%s", __METHOD__, $restriction->getType(), $restrictedField) );

            switch ($restriction->getType()) {
                case FactoryX_PromoRestriction_Model_Restriction::RESTRICT_EMAIL:
                    // Get the current quote email
                    $currentEmail = $observer->getEvent()->getQuote()->getCustomer()->getEmail();
                    if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
                        // Not logged in
                        $errMsg = Mage::helper('promorestriction')->__('This coupon code can only be used as a logged in customer');
                    }
                    elseif ($currentEmail != $restrictedField) {
                        // Different emails
                        $errMsg = Mage::helper('promorestriction')->__('This coupon code is restricted to another customer email');
                    }
                    break;
                case FactoryX_PromoRestriction_Model_Restriction::RESTRICT_IP:
                case FactoryX_PromoRestriction_Model_Restriction::RESTRICT_STORE:

                    $store = Mage::getResourceModel('ustorelocator/location_collection')
                        ->addFieldToSelect('ip_address')
                        ->addFieldToFilter('store_code', $restrictedField)
                        ->setPageSize(1);

                    if ($store->getSize()) {
                        $storeIpAddress = $store->getFirstItem()->getIpAddress();
                        $currentIp = Mage::helper('promorestriction')->getRemoteAddr();

                        //Mage::helper('promorestriction')->log(sprintf("%s->currentIp: %s", __METHOD__, $currentIp) );

                        if (filter_var($storeIpAddress, FILTER_VALIDATE_IP)) {
                            if (
                                !filter_var($currentIp, FILTER_VALIDATE_IP)
                                ||
                                $currentIp != $storeIpAddress
                            ) {
                                // Different ips
                                $errMsg = Mage::helper('promorestriction')->__('This coupon code is restricted to another IP address');
                            }
                        }
                        // comma delimited lists of ip addresses
                        elseif(preg_match('/\b((25[0-5]|2[0-4]\d|[01]?\d{1,2})\.){3}(25[0-5]|2[0-4]\d|[01]?\d{1,2})\b/', $storeIpAddress)) {
                            $ipAddresses = preg_split('/\,/', $storeIpAddress);
                            $found = false;
                            foreach($ipAddresses as $ipAddress) {
                                if ($currentIp == $ipAddress) {
                                    $found = true;
                                }
                            }
                            if (!$found) {
                                $errMsg = Mage::helper('promorestriction')->__('This coupon code is restricted to another IP address');
                            }
                        }
                        else {
                            $errMsg = Mage::helper('promorestriction')->__('This coupon code cannot be validated');
                        }
                    }
                    break;

                default:
                    $errMsg = Mage::helper('promorestriction')->__('This coupon has an unsupported restriction type "%s"!', $restriction->getType());
            }
        }
        // handle errors
        if ($errMsg) {
            Mage::getSingleton('checkout/session')->addError($errMsg);
            if ($code = $observer->getEvent()->getQuote()->getCouponCode()) {
                //Mage::helper('promorestriction')->log(sprintf("remove old code: %s", $code));
                $observer->getEvent()->getQuote()->setCouponCode('');
                // do we need to save ???
                //$observer->getEvent()->getQuote()->collectTotals()->save();
            }
        }
    }

}