<?php

class FactoryX_Westfield_Helper_Data extends Mage_Core_Helper_Abstract
{
	/*
	 *	Check if module is enabled
	 */
	public function isEnabled()
	{
		return Mage::getStoreConfig('westfield/options/enabled');
	}
	
	/*
	 *	Get the Westfield Campaign ID
	 */
	public function getCampaignId()
	{
		return Mage::getStoreConfig('westfield/options/campaign_id');
	}

	/*
	 *	Get the domain to track
	 */
	public function getTrackSource()
	{
		return Mage::getStoreConfig('westfield/options/track_source');
	}

}