<?php

/**
 * Class FactoryX_CustomerSurvey_Helper_Data
 */
class FactoryX_CustomerSurvey_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $logFileName = 'factoryx_customersurvey.log';

    /**
     * Log data
     * @param string|object|array data to log
     */
    public function log($data)
    {
        Mage::log($data, null, $this->logFileName);
    }
}