<?php
/**
 * @package 
 * @author FactoryX Developers (raphael@factoryx.com.au)
 */ 
class FactoryX_Gua_Helper_Data extends Mage_Core_Helper_Abstract {

    protected $logFileName = 'factoryx_gua.log';

    /**
     * Log data
     * @param string|object|array data to log
     */
    public function log($data)
    {
        Mage::log($data, null, $this->logFileName);
    }

}