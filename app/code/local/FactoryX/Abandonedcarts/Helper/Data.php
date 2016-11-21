<?php

/**
 * Class FactoryX_Abandonedcarts_Helper_Data
 */
class FactoryX_Abandonedcarts_Helper_Data extends Mage_Core_Helper_Abstract
{
	protected $logFileName = 'factoryx_abandonedcarts.log';

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
		return Mage::getStoreConfigFlag('abandonedcartsconfig/options/enable');
	}

	/**
	 * @return mixed
	 */
	public function isSaleEnabled()
	{
		return Mage::getStoreConfigFlag('abandonedcartsconfig/options/enable_sale');
	}

	/**
	 * @return mixed
	 */
	public function getDryRun()
	{
		return Mage::getStoreConfigFlag('abandonedcartsconfig/test/dryrun');
	}

	/**
	 * @return mixed
	 */
	public function getTestEmail()
	{
		return Mage::getStoreConfig('abandonedcartsconfig/test/testemail');
	}

	/**
	 * @return mixed
	 */
	public function getCustomerGroupsLimitation()
	{
		return explode(',',Mage::getStoreConfig('abandonedcartsconfig/options/customer_groups'));
	}

	/**
	 * @return bool
	 */
	public function isCampaignEnabled()
	{
		return Mage::getStoreConfigFlag('abandonedcartsconfig/campaign/enable');
	}

	/**
	 * @return mixed
	 */
	public function getCampaignName()
	{
		return Mage::getStoreConfig('abandonedcartsconfig/campaign/name');
	}

	/**
	 * @return bool
	 */
	public function isAutologin()
	{
		return Mage::getStoreConfigFlag('abandonedcartsconfig/email/autologin');
	}

	/**
	 * @return bool
	 */
	public function isShortenerEnabled()
	{
		return Mage::getStoreConfigFlag("abandonedcartsconfig/url/enabled");
	}

	/**
	 * @return mixed
	 */
	public function getGoogleApi()
	{
		return Mage::getStoreConfig('abandonedcartsconfig/url/google_api_key');
	}

	/**
	 * @return mixed
	 */
	public function getProxy()
	{
		return Mage::getStoreConfig('abandonedcartsconfig/url/http_proxy');
	}

	/**
	 * @param $url
	 * @param bool $extended
	 * @return mixed|string
	 */
	public function shortenUrl($url, $extended = false)
	{
		$newUrl = "";
		try {
			include_once MAGENTO_ROOT . '/lib/googl-php/Googl.class.php';
			$googl = new Googl(
				$this->getGoogleApi(),
				$this->getProxy()
			);
			$newUrl = $googl->shorten($url, $extended);
		}
		catch(Exception $ex) {
			$this->log(sprintf("%s->error: %s", __METHOD__, $ex->getMessage()) );
			$newUrl = $url;
		}
		return $newUrl;
	}

}