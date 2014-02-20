<?php
include_once MAGENTO_ROOT . "/lib/createsend/csrest_lists.php";

class FactoryX_CampaignMonitor_Helper_Data extends Mage_Core_Helper_Abstract
{
	protected $logFileName = 'factoryx_campaignmonitor.log';
	
    public function isCheckoutNewsletterEnabled()
    {
        return Mage::getStoreConfigFlag('newsletter/checkoutnewsletter/enable');
    }

    public function isCheckoutNewsletterChecked()
    {
        return Mage::getStoreConfigFlag('newsletter/checkoutnewsletter/checked');
    }

    public function isCheckoutNewsletterVisibleGuest()
    {
        return Mage::getStoreConfigFlag('newsletter/checkoutnewsletter/visible_guest');
    }

    public function isCheckoutNewsletterVisibleRegister()
    {
        return Mage::getStoreConfigFlag('newsletter/checkoutnewsletter/visible_register');
    }
	
	public function isCouponEnabled()
	{
		return Mage::getStoreConfigFlag('newsletter/coupon/enable');
	}
	
	public function getCouponPrefix()
	{
		return Mage::getStoreConfig('newsletter/coupon/prefix');
	}

	public function getCouponMinSpend()
	{
		return Mage::getStoreConfig('newsletter/coupon/spend');
	}
	
	public function getCouponValue()
	{
		return Mage::getStoreConfig('newsletter/coupon/value');
	}

	public function getCouponOffer()
	{
		return Mage::getStoreConfig('newsletter/coupon/offer');
	}
	
	public function getCouponValidity()
	{
		return Mage::getStoreConfig('newsletter/coupon/valid');
	}
	
	public function getCampaignMonitorStates()
	{
		$apiKey = trim(Mage::getStoreConfig('newsletter/campaignmonitor/api_key'));
        $listID = trim(Mage::getStoreConfig('newsletter/campaignmonitor/list_id'));
		
		try 
		{
			$client = new CS_REST_Lists($listID,$apiKey);
		} catch(Exception $e) {
			$this->log("Error connecting to CampaignMonitor server: ".$e->getMessage());
			$session->addException($e, $this->__('There was a problem retrieving the states'));
		}
		
		try 
		{
			$result = $client->get_custom_fields();
			if ($result->was_successful()) 
			{
				$states = array();
				foreach ($result->response as $customField)
				{
					if ($customField->FieldName == "State")
					{
						foreach ($customField->FieldOptions as $customFieldOption)
						{
							$states[] = $customFieldOption;
						}
						return $states;
					}
				}
			}
			return false;
		} catch(Exception $e) {
			$this->log("Error in CampaignMonitor SOAP call: ".$e->getMessage());
			$session->addException($e, $this->__('There was a problem with the subscription'));
			$this->_redirectReferer();
		}
	}
	
	public function getStores()
	{
		$stores = array();
		$stores['NULL'] = "None selected"; // used for grids
		$storesColl = Mage::getModel('ustorelocator/location')->getCollection();    	
		foreach($storesColl as $store) 
		{
			$stores[$store->getStoreCode()] = $store->getTitle();
		}
		asort($stores);
		return $stores;
	}
	
	/**
	 * Log data
	 * @param string|object|array data to log
	 */
	public function log($data) 
	{
		Mage::log($data, null, $this->logFileName);
	}
	
}
