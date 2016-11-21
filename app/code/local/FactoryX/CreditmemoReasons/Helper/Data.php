<?php

/**
 * Class FactoryX_CreditmemoReasons_Helper_Data
 */
class FactoryX_CreditmemoReasons_Helper_Data extends Mage_Core_Helper_Abstract
{
	protected $logFileName = 'factoryx_creditmemoreasons.log';

    /**
     * @param $identifier
     * @return bool
     */
    public function getReasonLabel($identifier)
    {
        $reasons = Mage::getModel('creditmemoreasons/system_config_source_reasons')->toOptionHash('identifier','title');
        if (array_key_exists($identifier,$reasons))
            return $reasons[$identifier];
        else
            return false;
    }
    
    public function getReasonsArray()
    {
        $reasons = Mage::getModel('creditmemoreasons/system_config_source_reasons')->toOptionArray();
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
