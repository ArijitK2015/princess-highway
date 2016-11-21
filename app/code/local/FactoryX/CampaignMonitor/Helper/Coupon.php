<?php
/**
 * Class FactoryX_CampaignMonitor_Helper_Coupon
 */
class FactoryX_CampaignMonitor_Helper_Coupon extends Mage_Core_Helper_Abstract
{
    
    const DEFAULT_COUPON_LENGTH = 7;
    const DEFAULT_COUPON_VALIDITY = 14;
    const DEFAULT_COUPON_VALUE = 10;
    const DEFAULT_COUPON_TYPE = "by_percent";
    const DEFAULT_COUPON_USES = 1;
    const DEFAULT_COUPON_USES_CUSTOMER = 1;
    const DEFAULT_COUPON_PRIORITY = 1;
    const DEFAULT_COUPON_STOP_FURTHER = 1;

    const DEFAULT_DATE_FORMAT = "D \\t\\h\\e jS \\o\\f F Y";    
    
    /**
     * @return bool
     */
    public function isCouponEnabled()
    {
        //return Mage::getStoreConfigFlag('newsletter/coupon/enable');
        return Mage::getStoreConfigFlag('newsletter/subscription/generate_coupon');
    }

    /**
     * @return mixed
     */
    public function getCouponPrefix()
    {
        return Mage::getStoreConfig('newsletter/coupon/prefix');
    }

    /**
     * return int
     */
    public function getCouponLength() {
        $length = self::DEFAULT_COUPON_LENGTH;
        if (
            Mage::getStoreConfig('newsletter/coupon/length')
            &&
            is_numeric(Mage::getStoreConfig('newsletter/coupon/length'))
        ) {
            $length = Mage::getStoreConfig('newsletter/coupon/length');
        }
        return $length;
    }    

    /**
     * @return mixed
     */
    public function getCouponType()
    {
        $coupontype = Mage::getStoreConfig('newsletter/coupon/offer');
        if (empty($coupontype)) {
            $coupontype = self::DEFAULT_COUPON_TYPE;
        }
        return $coupontype;
    }

    /**
     * @return mixed
     */
    public function getCoupon()
    {
        return Mage::getStoreConfig('newsletter/coupon/coupon');
    }

    /**
     * @return mixed
     */
    public function getCouponSpend()
    {
        return Mage::getStoreConfig('newsletter/coupon/spend');
    }

    /**
     * @return mixed
     */
    public function getCouponValue()
    {
        // coupon value
        $value = Mage::getStoreConfig('newsletter/coupon/value');
        if (!is_numeric($value)) {
            $value = self::DEFAULT_COUPON_VALUE;
        }
        return intval($value);
    }

    /**
     * @return mixed
     */
    public function getCouponValidFor()
    {
        $validity = "";
        if (Mage::getStoreConfig('newsletter/coupon/valid_for')) {
            $validity = Mage::getStoreConfig('newsletter/coupon/valid_for');
            if (!is_numeric($validity)) {
                $validity = self::DEFAULT_COUPON_VALIDITY;
            }
        }
        return $validity;
    }

    /**
     * @return string
     */
    public function getDateFormat() {
        $dateFormat = self::DEFAULT_DATE_FORMAT;
        if (Mage::getStoreConfig('newsletter/coupon/date_format')) {
            $dateFormat = Mage::getStoreConfig('newsletter/coupon/date_format');
        }
        return $dateFormat;
    }

    /**
     * @return int
     */
    public function getCouponPriority() {
        $priority = self::DEFAULT_COUPON_PRIORITY;
        if (Mage::getStoreConfig('newsletter/coupon/priority')) {
            $priority = Mage::getStoreConfig('newsletter/coupon/priority');
        }
        return $priority;
    }

    /**
     * @return int
     */
    public function getCouponStopRulesProcessing() {
        $stop = self::DEFAULT_COUPON_STOP_FURTHER;
        if (Mage::getStoreConfig('newsletter/coupon/stop')) {
            $stop = Mage::getStoreConfig('newsletter/coupon/stop');
        }
        return $stop;
    }

    /**
     * @return mixed
     */
    public function getCouponValidTill()
    {
        return Mage::getStoreConfig('newsletter/coupon/valid_till');
    }

    /**
     * @return mixed
     */
    public function getUsesCoupon()
    {
        $usesCoupon = Mage::getStoreConfig('newsletter/coupon/uses_coupon');
        if (!is_numeric($usesCoupon)) {
            $usesCoupon = self::DEFAULT_COUPON_USES;
        }
        return $usesCoupon;
    }

    /**
     * @return mixed
     */
    public function getUsesCustomer()
    {
        $usesCustomer = Mage::getStoreConfig('newsletter/coupon/uses_customer');
        if (!is_numeric($usesCustomer)) {
            $usesCustomer = self::DEFAULT_COUPON_USES_CUSTOMER;
        }
        return $usesCustomer;
    }    
    
    /**
     * @return mixed
     */
    public function getCustomerGroups()
    {
        return explode(',',Mage::getStoreConfig('newsletter/coupon/customer_groups'));
    }

    /**
     * @return mixed
     */
    public function isRestricted()
    {
        return Mage::getStoreConfigFlag('newsletter/coupon/restrict');
    }

    /**
     * defaults to "Default Rule Label for All Store Views"
     *
     * return array
     */
    public function getStoreLabels() {
        $labels = array();
        if (Mage::getStoreConfig('newsletter/coupon/label')) {
            $label = Mage::getStoreConfig('newsletter/coupon/label');
            if (preg_match("/%OFFER%/", $label)) {
                if ($this->getCouponType() == Mage_SalesRule_Model_Rule::CART_FIXED_ACTION) {
                    $offer = sprintf("$%s", $this->getCouponValue());
                }
                else if ($this->getCouponType() == Mage_SalesRule_Model_Rule::BY_PERCENT_ACTION) {
                    $offer = sprintf("%s%%", $this->getCouponValue());
                }
                $label = preg_replace("/%OFFER%/", $offer, $label);
            }
            $labels[] = $label;
        }
        return $labels;
    }

    /**
     * return array
     */
    public function getWebsiteIds() {
        $websiteIds = array();
        foreach (Mage::app()->getWebsites() as $website) {
            $websiteIds[] = $website->getId();
        }
        return $websiteIds;
    }

    public function _generatePromocode($subscriber) {
        // Name of the rule
        $name = sprintf("newsletter subscription coupon for %s", $subscriber->getSubscriberEmail());
        $description = $name;

        // Validity
        $ts = Mage::getModel('core/date')->timestamp(time());
        $fromDate = date('Y-m-d', $ts);

        $validdays = $this->getCouponValidFor();

        if ($validtill = $this->getCouponValidTill()) {
            $tmp = new Zend_Date($validtill, 'd/M/Y');
            $validtillts = $tmp->getTimestamp();
            $toDate = date('Y-m-d', $validtillts);
            $description .= sprintf(" valid [%s - %s]", date("Y-m-d H:i:s", $ts), date('Y-m-d H:i:s', $validtillts));
            if ($ts > $validtillts) {
                return false;
            }
        }
        else {
            // Add validity
            $toDate = date('Y-m-d', strtotime(sprintf("%s +%d days", date("Y-m-d", $ts), $validdays)));
            $description .= sprintf(" valid [%s - %s]", date("Y-m-d H:i:s", $ts), date('Y-m-d H:i:s', strtotime(sprintf("%s +%d days", date("Y-m-d", $ts), $validdays))));
        }
        // Generate the hash code for the coupon
        $coupon_code = sprintf("%s%s", $this->getCouponPrefix(), $this->_generateHashCode());

        /**
        to work out actions & conditions see Mage_Salesrule_Model_Rule::loadPost()
         */

        $conditions = array();
        if ($this->getCouponSpend()) {
            $conditions = array(
                "1" => array(
                    "type"          => "salesrule/rule_condition_combine",
                    "aggregator"    => "all",
                    "value"         => "1",
                    "new_child"     => false
                ),
                "1--1" => array(
                    "type"          => "salesrule/rule_condition_address",
                    "attribute"     => "base_subtotal",
                    "operator"      => ">=",
                    "value"         => $this->getCouponSpend()
                )
            );
        }
        
        /*
        // old method, see _generateRuleActionSerialized which uses a less complex array
        $actions = array(
            "1" => array(
                "type"          => "salesrule/rule_condition_product_combine",
                "aggregator"    => "all",
                "value"         => 1,
                "new_child"     => false
            )
        );
        */

        // Create the coupon and save it
        $coupon = Mage::getModel('salesrule/rule');

        $actions = Mage::helper('campaignmonitor/coupon')->_generateRuleActionSerialized();
        $coupon->setName($name)
            ->setDescription($description)
            ->setFromDate($fromDate)
            ->setToDate($toDate)
            ->setCouponCode($coupon_code)
            ->setUsesPerCoupon(Mage::helper('campaignmonitor/coupon')->getUsesCoupon())
            ->setUsesPerCustomer(Mage::helper('campaignmonitor/coupon')->getUsesCustomer())            
            ->setCustomerGroupIds(Mage::helper('campaignmonitor/coupon')->getCustomerGroups())
            ->setCouponType(Mage_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC)
            ->setIsActive(1)
            // TODO: use setConditionsSerialized
            ->setData('conditions', $conditions)
            //->setConditionsSerialized('a:7:{s:4:"type";s:32:"salesrule/rule_condition_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";s:10:"conditions";a:1:{i:0;a:7:{s:4:"type";s:42:"salesrule/rule_condition_product_subselect";s:9:"attribute";s:14:"base_row_total";s:8:"operator";s:2:">=";s:5:"value";s:3:"300";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";s:10:"conditions";a:2:{i:0;a:5:{s:4:"type";s:32:"salesrule/rule_condition_product";s:9:"attribute";s:13:"special_price";s:8:"operator";s:3:"!{}";s:5:"value";s:1:"0";s:18:"is_value_processed";b:0;}i:1;a:5:{s:4:"type";s:32:"salesrule/rule_condition_product";s:9:"attribute";s:3:"sku";s:8:"operator";s:3:"!{}";s:5:"value";s:4:"gift";s:18:"is_value_processed";b:0;}}}}}')            
            ->setActionsSerialized($actions)
            ->setStopRulesProcessing(0)
            ->setIsAdvanced(0)
            ->setProductIds('')
            ->setSortOrder(Mage::helper('campaignmonitor/coupon')->getCouponPriority())
            ->setSimpleAction(Mage::helper('campaignmonitor/coupon')->getCouponType())            
            ->setDiscountAmount($this->getCouponValue())
            ->setDiscountQty(null)
            ->setDiscountStep('0')
            ->setSimpleFreeShipping('0')
            ->setApplyToShipping('0')
            ->setIsRss(0)
            ->setWebsiteIds(Mage::helper('campaignmonitor/coupon')->getWebsiteIds())
            ->setStopRulesProcessing(Mage::helper('campaignmonitor/coupon')->getCouponStopRulesProcessing())
            ->setStoreLabels(Mage::helper('campaignmonitor/coupon')->getStoreLabels());

        /*
        $coupon->setData('conditions', $conditions)
        $coupon->setData('actions', $actions)
        */
        $coupon->loadPost($coupon->getData());
        $coupon->save();

        // If restricted flag
        if ($this->isRestricted()) {
            // Create a restriction
            Mage::getModel('promorestriction/restriction')->setData(
                [
                    'salesrule_id'				=> 	$coupon->getRuleId(),
                    'restricted_field'  		=>	$subscriber->getSubscriberEmail(),
                    'type'						=>	FactoryX_PromoRestriction_Model_Restriction::RESTRICT_EMAIL
                ]
            )->save();
        }

        return $coupon_code;
    }

    /**
     * @param $subscriber
     */
    public function handleCoupon($subscriber)
    {
        if ($this->isCouponEnabled() && !$subscriber->getSubscriberCoupon()) {
            //$couponcode = $this->_generateAutoPromocode($subscriber);
            $couponcode = $this->_generatePromocode($subscriber);
            if ($couponcode) {
                $subscriber->setSubscriberCoupon($couponcode);
            }
        }
    }

    /**
     * Generate Hash Code for the coupon
     * @return string
     */
    protected function _generateHashCode() {
        $seed = 'JvKnrQWPsThuJteNQAuH' . date("Y-m-d");
        $hash = sha1(uniqid($seed . mt_rand(), true));
        //$this->log(sprintf("hash[%d]:%s", strlen($hash), $hash));
        $length = $this->getCouponLength();
        if ($length > strlen($hash)) {
            $length = strlen($hash);
        }
        return substr(strtoupper($hash), 0, $length);
    }

    /**
     * @return mixed
     */
    protected function _getCouponValue()
    {
        if ($ruleId = $this->getCoupon())
        {
            $collection = Mage::getResourceModel('salesrule/rule_collection')
                ->addFieldToSelect('discount_amount')
                ->addFieldToFilter('rule_id',$ruleId)
                ->setPageSize(1);

            if ($collection->getSize())
            {
                return $collection->getFirstItem()->getDiscountAmount();
            }
            return false;
        }
    }

    /**
     * @param $subscriber
     * @return string
     */
    protected function _generateAutoPromocode($subscriber)
    {
        if (!$this->isCouponEnabled())
        {
            return '';
        }

        if ($ruleId = $this->getCoupon())
        {
            $prefix = $this->getCouponPrefix();
            if (!$prefix)
            {
                $prefix = "";
            } else {
                $prefix .= "_";
            }

            $salesRule = Mage::getModel('salesrule/rule')->load($ruleId);

            if ($salesRule->getId())
            {
                $generator = Mage::getModel('salesrule/coupon_massgenerator');
                $coupon_length = $this->getCouponLength();
                $data = array(
                    'qty' => 1, //number of coupons to generate
                    'length' => $coupon_length, //length of coupon string
                    'prefix' => $prefix,
                    'format' => Mage_SalesRule_Helper_Coupon::COUPON_FORMAT_ALPHANUMERIC,
                    'rule_id' => $ruleId //the id of the rule you will use as a template
                );
                $generator->validateData($data);
                $generator->setData($data);
                $generator->generatePool();

                $code = Mage::getResourceModel('salesrule/coupon_collection')
                    ->addRuleToFilter($salesRule)
                    ->addGeneratedCouponsFilter()
                    ->getLastItem()
                    ->getData('code');

                return $code;
            }
        }
    }
    
    /**
     * Generates a magento sales rule and serialises the result
     *
     * @return string rule
     */
    protected function _generateRuleActionSerialized() {
        $excludeSku = Mage::getStoreConfig('newsletter/coupon/exclude_sku');
        $excludeSale = Mage::getStoreConfig('newsletter/coupon/exclude_sale');

        $conditions = array();

        if ($excludeSale) {
            $conditions[] = array(
                "type"      => "salesrule/rule_condition_product",
                "attribute" => "special_price",
                "operator"  => "!{}",
                "value"     => "0",
                "is_value_processed" => 0
            );
        }
        if ($excludeSku) {
            $skus = explode(",", $excludeSku);
            foreach ($skus as $sku) {
                $conditions[] = array(
                    "type"      => "salesrule/rule_condition_product",
                    "attribute" => "sku",
                    "operator"  => "!{}",
                    "value"     => $sku,
                    "is_value_processed" => 0
                );
            }
        }
        //$this->log(sprintf("%s->conditions: %s", __METHOD__, print_r($conditions, true)));
        $ruleAction = array(
            "type"                  => "salesrule/rule_condition_product_combine",
            "attribute"             => NULL,
            "operator"              => NULL,
            "value"                 => "1",
            "is_value_processed"    => NULL,
            "aggregator"            => "all",
            "conditions"            => $conditions
        );
        $ruleActionSerialized = serialize($ruleAction);
        //$this->log(sprintf("%s->ruleActionSerialized: %s", __METHOD__, $ruleActionSerialized));
        return $ruleActionSerialized;
    }    
}