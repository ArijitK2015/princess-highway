<?php

/**
 * Class FactoryX_AdvertPanels_Helper_Data
 */
class FactoryX_AdvertPanels_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * isValidURL function to test if an URL is provided in the description of the advertPanel
     * @param $url
     * @return int
     */
    function isValidURL($url)
	{
		return preg_match("#^http(s)?://[a-z0-9-_.]+\.[a-z]{2,4}#i", $url);
	}

}