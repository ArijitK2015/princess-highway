<?php
class FactoryX_AdvertPanels_Helper_Data extends Mage_Core_Helper_Abstract
{
	/*
	 * 	isValidURL function to test if an URL is provided in the description of the advertPanel
	 *	@param string url to test
	 *	@return boolean	0 if it's not valid 1 otherwise
	 */
	function isValidURL($url)
	{
		return preg_match("#^http(s)?://[a-z0-9-_.]+\.[a-z]{2,4}#i", $url);
	}

}