<?php
/**
 */
class FactoryX_Sales_Helper_Data extends Mage_Core_Helper_Abstract
{	
	protected $logFileName = 'factoryx_sales.log';
	
	/**
	 * Log data
	 * @param string|object|array data to log
	 */
	public function log($data) 
	{
		Mage::log($data, null, $this->logFileName);
	}
	
}
