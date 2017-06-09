<?php
/*
add restrictions to exiting promos
- newsletter/coupon
- bdayconfig/coupon

select * from core_resource where code like '%restriction%';
update core_resource set data_version = '0.1.3' where code = 'promorestriction_setup';

select * from fx_promo_restriction;

TODO:
- get code pattern from settings
- use walk for speed
*/

$installer = $this;
$installer->startSetup();

// welcome codes
$rulesCollection = Mage::getModel('salesrule/rule')->getCollection();

//$coupon = Mage::getModel('salesrule/coupon')->load($couponCode, 'code');
//$rule = Mage::getModel('salesrule/rule')->load($coupon->getRuleId());

Mage::helper('promorestriction')->log(sprintf("check %d rules", count($rulesCollection)));

$prefix = Mage::app()->getStore()->getConfig('newsletter/coupon/prefix');
$newsletterCouponPattern = null;
if (!empty($prefix)) {
    $length = Mage::app()->getStore()->getConfig('newsletter/coupon/length');
    $newsletterCouponPattern = sprintf("%s[A-Z0-9]{1,%s}", $prefix, $length);
}

$prefix = Mage::app()->getStore()->getConfig('bdayconfig/coupon/prefix');
$bdayconfigCouponPattern = null;
if (!empty($prefix)) {
    $length = Mage::app()->getStore()->getConfig('bdayconfig/coupon/length');
    $bdayconfigCouponPattern = sprintf("%s[A-Z0-9]{1,%s}", $prefix, $length);
}

$cnt = 0;
foreach($rulesCollection as $rule) {
    // ->getCoupons();
    $coupon = $rule->getCode();
    //Mage::helper('promorestriction')->log(sprintf("check code: %s|%d", $coupon, preg_match("/welcome[A-Z0-9]{1,7}/i", $coupon)));

    if (
        ($newsletterCouponPattern && preg_match(sprintf("/%s/i", $newsletterCouponPattern), $coupon))
        ||
        ($bdayconfigCouponPattern && preg_match(sprintf("/%s/i", $bdayconfigCouponPattern), $coupon))
    ) {
        $email = FactoryX_PromoRestriction_Helper_Data::extractEmailAddress($rule->getDescription());
        //Mage::helper('promorestriction')->log(sprintf("email: %s", $email));

        // If restricted flag
        if ($email) { // && $this->isRestricted()) {
            $cnt++;
            Mage::helper('promorestriction')->log(sprintf("restrict code '%s' with email: %s", $coupon, $email));
            $restriction = Mage::getModel('promorestriction/restriction')->load($rule->getId(), 'salesrule_id');
            if ($restriction) {
                Mage::helper('promorestriction')->log(sprintf("delete old restriction from: %d", $rule->getId()));
                $restriction->delete();
            }
            $data = array(
                'salesrule_id'        =>     $rule->getRuleId(),
                'type'              =>  FactoryX_PromoRestriction_Model_Restriction::RESTRICT_EMAIL,
                'restricted_field'    =>    $email
            );
            $restriction->setData($data)->save();
        }
    }
}
Mage::helper('promorestriction')->log(sprintf("updated %d rules", $cnt));

$installer->endSetup();