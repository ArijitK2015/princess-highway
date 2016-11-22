<?php

/**
 * Class FactoryX_CouponValidation_Model_Observer
 */
class FactoryX_CouponValidation_Model_Observer
{
    /**
     * @param Varien_Event_Observer $observer
     * @throws Exception
     */
    public function logRedeemedCoupons(Varien_Event_Observer $observer)
    {
        $coupon = $observer->getCoupon();
        $rule = $observer->getRule();
        $comment = $observer->getComment();

        $log = Mage::getModel('couponvalidation/log');
        $log->setData([
            'coupon_code'   => $coupon,
            'rule_id'       => $rule->getId(),
            'comment'       => $comment
        ]);

        if ($user = $observer->getUser()) {
            $log->setAdminUser($user);
        }

        if ($store = $observer->getStore()) {
            $log->setIpAddress($store->getIpAddress());
            $log->setStoreCode($store->getStoreCode());
        }

        $log->save();
    }
}