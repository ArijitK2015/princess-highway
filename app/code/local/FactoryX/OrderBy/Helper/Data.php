<?php

class FactoryX_OrderBy_Helper_Data extends Mage_Core_Helper_Abstract 
{
	protected $logFileName = 'factoryx_orderby.log';
	
	/**
	 * Log data
	 * @param string|object|array data to log
	 */
	public function log($data) 
	{
		Mage::log($data, null, $this->logFileName);
	}
    
}