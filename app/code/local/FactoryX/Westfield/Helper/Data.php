<?php

/**
 * Class FactoryX_Westfield_Helper_Data
 */
class FactoryX_Westfield_Helper_Data extends Mage_Core_Helper_Abstract {
    
    protected $logFileName = 'factoryx_westfield.log';
    
	/*
	 *	Check if module is enabled
	 */
	/**
	 * @return mixed
     */
	public function isEnabled() {
		return Mage::getStoreConfig('westfield/options/enabled');
	}
	
	/*
	 *	Get the Westfield Campaign ID
	 */
	/**
	 * @return mixed
     */
	public function getCampaignId() {
		return Mage::getStoreConfig('westfield/options/campaign_id');
	}

	/*
	 *	Get the query string
	 */
	/**
	 * @return array
     */
	public function getQueryString() {
	    $qs = array('key', 'value');
		$str = Mage::getStoreConfig('westfield/options/query_string');
		if (!empty($str) && preg_match("/^([a-zA-Z0-9\-\.\_\~]+)\=([a-zA-Z0-9\-\.\_\~]+)$/", $str)) {
		    $qs = explode("=", $str);
		}
		return $qs;
	}

	/*
	 *	Get the domain to track
	 */
	/**
	 * @return mixed
     */
	public function getTrackDomain() {
		return Mage::getStoreConfig('westfield/options/track_domain');
	}
	
    /**
     * isAdmin()
     *
     * use 2 methods of checking
     * 1. isAdmin - certain admin pages (the Magento Connect Package manager) where this isn't true
     * 2. check the "area" property of the design package.
     *
     * @return bool isAdmin
     */
    public function isAdmin() {
        if(Mage::app()->getStore()->isAdmin()) {
            return true;
        }
        if(Mage::getDesign()->getArea() == 'adminhtml') {
            return true;
        }
        return false;
    }	

	/**
	 * Log data
	 * @param string|object|array data to log
	 */
	public function log($data) {
		Mage::log($data, null, $this->logFileName);
	}
}