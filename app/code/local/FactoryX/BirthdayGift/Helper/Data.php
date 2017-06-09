<?php

/**
 * Class FactoryX_BirthdayGift_Helper_Data
 */
class FactoryX_BirthdayGift_Helper_Data extends Mage_Core_Helper_Abstract {

	protected $logFileName = 'factoryx_birthdaygift.log';

	const DEFAULT_COUPON_LENGTH = 7;
	const DEFAULT_COUPON_VALIDITY = 14;
	const DEFAULT_COUPON_VALUE = 10;
	const DEFAULT_COUPON_TYPE = "by_percent";
	const DEFAULT_COUPON_USES = 1;
	const DEFAULT_COUPON_USES_CUSTOMER = 1;

	const DEFAULT_DATE_FORMAT = "D \\t\\h\\e jS \\o\\f F Y";

	/**
	 * Log data
	 * @param string|object|array data to log
	 */
	public function log($data)
	{
		Mage::log($data, null, $this->logFileName);
	}

	/**
	 * @return mixed
	 */
	public function isEnabled()
	{
		return Mage::getStoreConfigFlag('bdayconfig/options/enable');
	}

	/**
	 * @return mixed
	 */
	public function getSenderEmail()
	{
		return Mage::getStoreConfig('bdayconfig/options/email');
	}

	/**
	 * @return mixed
	 */
	public function getSenderName()
	{
		return Mage::getStoreConfig('bdayconfig/options/name');
	}

	/**
	 * @return mixed
	 */
	public function getTemplate()
	{
		return Mage::getStoreConfig('bdayconfig/options/template');
	}

	/**
	 * @return mixed
	 */
	public function getCouponPrefix()
	{
		$prefix = Mage::getStoreConfig('bdayconfig/coupon/prefix');
		return $prefix;
	}

	/**
	 * @return mixed
	 */
	public function getCouponValue()
	{
		// coupon value
		$value = Mage::getStoreConfig('bdayconfig/coupon/value');
		if (!is_numeric($value)) {
			$value = self::DEFAULT_COUPON_VALUE;
		}
		return intval($value);
	}

	/**
	 * @return mixed
	 */
	public function getCouponValidity()
	{
		$validity = "";
		if (Mage::getStoreConfig('bdayconfig/coupon/valid')) {
			$validity = Mage::getStoreConfig('bdayconfig/coupon/valid');
			if (!is_numeric($validity)) {
				$validity = self::DEFAULT_COUPON_VALIDITY;
			}
		}
		return $validity;
	}

	/**
	 * @return string
	 */
	public function getDateFormat()
	{
		$dateFormat = self::DEFAULT_DATE_FORMAT;
		if (Mage::getStoreConfig('bdayconfig/coupon/date_format')) {
			$dateFormat = Mage::getStoreConfig('bdayconfig/coupon/date_format');
		}
		return $dateFormat;
	}

	/**
	 * @return mixed
	 */
	public function getCouponExcludeSku()
	{
		return Mage::getStoreConfig('bdayconfig/coupon/exclude_sku');
	}

	/**
	 * @return mixed
	 */
	public function getCouponExcludeSale()
	{
		return Mage::getStoreConfig('bdayconfig/coupon/exclude_sale');
	}

	/**
	 * @return mixed
	 */
	public function getDryRun()
	{
		return Mage::getStoreConfigFlag('bdayconfig/options/dryrun');
	}

	/**
	 * @return mixed
	 */
	public function isRestricted()
	{
		return Mage::getStoreConfigFlag('bdayconfig/coupon/restrict');
	}

	/**
	 * @return mixed
	 */
	public function getTestEmail()
	{
		return Mage::getStoreConfig('bdayconfig/options/testemail');
	}

	/**
	 * @return mixed
	 */
	public function getCustomerGroups()
	{
		return explode(',',Mage::getStoreConfig('bdayconfig/coupon/customer_groups'));
	}

	/**
	 * @return mixed
	 */
	public function getCouponType()
	{
		$coupontype = Mage::getStoreConfig('bdayconfig/coupon/type');
		if (empty($coupontype)) {
			$coupontype = self::DEFAULT_COUPON_TYPE;
		}
		return $coupontype;
	}

	/**
	 * @return mixed
	 */
	public function getUsesCoupon()
	{
		$usesCoupon = Mage::getStoreConfig('bdayconfig/coupon/uses_coupon');
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
		$usesCustomer = Mage::getStoreConfig('bdayconfig/coupon/uses_customer');
		if (!is_numeric($usesCustomer)) {
			$usesCustomer = self::DEFAULT_COUPON_USES_CUSTOMER;
		}
		return $usesCustomer;
	}

	/**
	 * @return mixed
	 */
	public function getWebsites()
	{
		$websites = array();
		foreach (Mage::app()->getWebsites() as $id => $website) {
			$websites[] = $id;
		}
		return $websites;
	}

	/**
	 * defaults to "Default Rule Label for All Store Views"
	 *
	 * return array
	 */
	public function getStoreLabels()
	{
		$labels = array();
		if (Mage::getStoreConfig('bdayconfig/coupon/label')) {
			$labels[] = Mage::getStoreConfig('bdayconfig/coupon/label');
		}
		return $labels;
	}

	/**
	 * return int
	 */
	public function getCouponLength()
	{
		$length = self::DEFAULT_COUPON_LENGTH;
		if (Mage::getStoreConfig('bdayconfig/coupon/length')
			&& is_numeric(Mage::getStoreConfig('bdayconfig/coupon/length'))
		) {
			$length = Mage::getStoreConfig('bdayconfig/coupon/length');
		}
		return $length;
	}

	/**
	 * Generate Hash Code for the coupon
	 * @return string
	 */
	public function generateHashCode()
	{
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
	 * Create new coupon code
	 * @param string $text
	 * @param $percentage
	 * @param $prefix
	 * @param $validdays
	 * @return string
	 * @throws Exception
	 */
	public function createSalesRule($text = "Unknown")
	{
		// Name of the rule
		$name = sprintf("birthday coupon for %s", $text);
		$description = $name;

		// Validity for two weeks
		$ts = Mage::getModel('core/date')->timestamp(time());
		$fromDate = date('Y-m-d', $ts);

		// Add the number of valid days
		$toDate = null;
		if (Mage::helper('birthdaygift')->getCouponValidity()) {
			$toDate = date('Y-m-d', strtotime(sprintf("%s +%d days", date("Y-m-d", $ts), Mage::helper('birthdaygift')->getCouponValidity())) );
			Mage::helper('birthdaygift')->log(sprintf("%s->toDate: %s", __METHOD__, $toDate));
			$description .= sprintf(" valid [%s - %s]", date("Y-m-d H:i:s", $ts), date('Y-m-d H:i:s', strtotime(sprintf("%s +%d days", date("Y-m-d", $ts), Mage::helper('birthdaygift')->getCouponValidity())) ));
		}

		// Generate the hash code for the coupon
		$couponCode = Mage::helper('birthdaygift')->generateHashCode();
		if (Mage::helper('birthdaygift')->getCouponPrefix()) {
			$couponCode = sprintf("%s%s", Mage::helper('birthdaygift')->getCouponPrefix(), $couponCode);
		}
		Mage::helper('birthdaygift')->log(sprintf("%s->couponCode: %s", __METHOD__, $couponCode));

		// Create the coupon and save it
		$coupon = Mage::getModel('salesrule/rule');

		// Generate the rule actions
		$action = Mage::helper('birthdaygift')->_generateRuleActionSerialized();
		// Assign data
		$coupon->setName($name)
			->setDescription($description)
			->setFromDate($fromDate)
			->setToDate($toDate)
			->setCouponCode($couponCode)
			->setUsesPerCoupon(Mage::helper('birthdaygift')->getUsesCoupon())
			->setUsesPerCustomer(Mage::helper('birthdaygift')->getUsesCustomer())
			->setCustomerGroupIds(Mage::helper('birthdaygift')->getCustomerGroups())
			->setCouponType('2')
			->setIsActive(1)
			->setConditionsSerialized('a:6:{s:4:"type";s:32:"salesrule/rule_condition_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";}')
			->setActionsSerialized($action)
			->setStopRulesProcessing(1)
			->setIsAdvanced(0)
			->setProductIds('')
			->setSortOrder(0)
			->setSimpleAction(Mage::helper('birthdaygift')->getCouponType())
			->setDiscountAmount(Mage::helper('birthdaygift')->getCouponValue())
			->setDiscountQty(null)
			->setDiscountStep('0')
			->setSimpleFreeShipping('0')
			->setApplyToShipping('0')
			->setIsRss(0)
			->setWebsiteIds(Mage::helper('birthdaygift')->getWebsites())
			->setStoreLabels(Mage::helper('birthdaygift')->getStoreLabels());

		$coupon->save();

		// If restricted flag
		if ($this->isRestricted()) {
			// Create a restriction
			Mage::getModel('promorestriction/restriction')->setData(
				[
					'salesrule_id'				=> 	$coupon->getRuleId(),
					'restricted_field'  		=>	$text,
					'type'						=>	FactoryX_PromoRestriction_Model_Restriction::RESTRICT_EMAIL
				]
			)->save();
		}

		return $coupon;
	}

	/**
	 * Generates a magento sales rule and serialises the result
	 *
	 * @return string rule
	 */
	protected function _generateRuleActionSerialized()
	{
		$excludeSku = Mage::getStoreConfig('bdayconfig/coupon/exclude_sku');
		$excludeSale = Mage::getStoreConfig('bdayconfig/coupon/exclude_sale');

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