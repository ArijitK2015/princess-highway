<?php

class FactoryX_CouponValidation_Model_Validator
{
    /**
    $data = array(
        'code'              => // coupon cdoe
        'website'           => // website
        'customer'          => // customer
        'customer_group'    => // customer group
    )
    */
    public function validate($data)
    {
        $coupon = Mage::getModel('salesrule/coupon')->load($data['code'],'code');

        // Validate coupon existence
        if (!$coupon->getId()) {
            Mage::throwException(Mage::helper('couponvalidation')->__('Coupon %s does not exist',$data['code']));
        }

        $rule = Mage::getModel('salesrule/rule')->load($coupon->getRuleId());

        // Validate if rule is active
        if (!$rule->getIsActive()) {
            Mage::throwException(Mage::helper('couponvalidation')->__('Coupon %s is not valid, rule is not active',$data['code']));
        }

        // Validate websites
        if (array_key_exists('websites', $data)) {
            $websiteIds = $rule->getWebsiteIds();
            if (!in_array($data['website'], $websiteIds)) {
                $websiteNames = Mage::getResourceModel('core/website_collection')
                    ->addFieldToFilter('website_id', array('in' => $websiteIds))
                    ->getColumnValues('name');

                Mage::throwException(Mage::helper('couponvalidation')->__('Your coupon is not valid for this store. Allowed Websites: %s',implode(', ', $websiteNames)));
            }
        }

        // Validate customer groups
        if (array_key_exists('customer_group', $data)) {
            $groupIds = $rule->getCustomerGroupIds();
            if (!in_array($data['customer_group'], $groupIds)) {
                $customerGroupNames = Mage::getResourceModel('customer/group_collection')
                    ->addFieldToFilter('customer_group_id', array('in' => $groupIds))
                    ->getColumnValues('customer_group_code');
                Mage::throwException(Mage::helper('couponvalidation')->__('Your coupon is not valid for this customer group. Allowed Customer Groups: %s',implode(', ', $customerGroupNames)));
            }
        }

        $today = Mage::app()->getLocale()->date(null, Varien_Date::DATETIME_INTERNAL_FORMAT);

        if ($coupon->getExpirationDate()) {
            // Validate coupon expiration date
            $expirationDate = Mage::app()->getLocale()->date($coupon->getExpirationDate(), Varien_Date::DATETIME_INTERNAL_FORMAT);
            if ($today->isLater($expirationDate, Zend_Date::DATE_MEDIUM)) {
                Mage::throwException(Mage::helper('couponvalidation')->__('Coupon is no longer valid, it expired on %s',Mage::helper('core')->formatDate($expirationDate, Mage_Core_Model_Locale::FORMAT_TYPE_LONG)));
            }
        }

        // Validate if from date is earlier
        $fromDate = Mage::app()->getLocale()->date($rule->getFromDate(), Varien_Date::DATETIME_INTERNAL_FORMAT);
        if ($today->isEarlier($fromDate, Zend_Date::DATE_MEDIUM)) {
            Mage::throwException(Mage::helper('couponvalidation')->__('Rule is not valid yet, it will be active on %s',Mage::helper('core')->formatDate($fromDate, Mage_Core_Model_Locale::FORMAT_TYPE_LONG)));
        }

        // Validate if to date is later
        $toDate = Mage::app()->getLocale()->date($rule->getToDate(), Varien_Date::DATETIME_INTERNAL_FORMAT);
        if ($today->isLater($toDate, Zend_Date::DATE_MEDIUM)) {
            Mage::throwException(Mage::helper('couponvalidation')->__('Rule is no longer valid, it expired on %s',Mage::helper('core')->formatDate($toDate, Mage_Core_Model_Locale::FORMAT_TYPE_LONG)));
        }

        // Validate global usage limit
        if ($coupon->getUsageLimit() && $coupon->getTimesUsed() >= $coupon->getUsageLimit()) {
            Mage::throwException(Mage::helper('couponvalidation')->__('Coupon was already used. It may only be used %d time(s).',$coupon->getUsageLimit()));
        }

        if (array_key_exists('customer', $data) && $data['customer']) {
            // Validate per customer usage limit
            if ($coupon->getUsagePerCustomer()) {
                $couponUsage = new Varien_Object();
                Mage::getResourceModel('salesrule/coupon_usage')->loadByCustomerCoupon($couponUsage, $data['customer'], $coupon->getId());
                if ($coupon->getCouponId() && $couponUsage->getTimesUsed() >= $coupon->getUsagePerCustomer()) {
                    Mage::throwException(Mage::helper('couponvalidation')->__('This customer has already used this coupon. It may only be used %d time(s).',$coupon->getUsagePerCustomer()));
                }
            }

            // Validate per rule usage limit
            if ($rule->getId() && $rule->getUsesPerCustomer()) {
                $ruleCustomer = Mage::getModel('salesrule/rule_customer');
                $ruleCustomer->loadByCustomerRule($data['customer'], $rule->getId());
                if ($ruleCustomer->getId()) {
                    if ($ruleCustomer->getTimesUsed() >= $rule->getUsesPerCustomer()) {
                        Mage::throwException(Mage::helper('couponvalidation')->__('This customer has already used this coupon. It may only be used %d time(s).',$coupon->getUsagePerCustomer()));
                    }
                }
            }
        }

        return true;
    }
}