<?php
class FactoryX_ExtendedApi_Helper_Data extends Mage_Core_Helper_Abstract {

	protected $logFileName = 'factoryx_extendedapi.log';
	
	/**
	 * Log data
	 * @param string|object|array data to log
	 */
	public function log($data) 
	{
		Mage::log($data, null, $this->logFileName);
	}
}

