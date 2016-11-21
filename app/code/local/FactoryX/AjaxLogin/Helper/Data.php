<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 10/11/2014
 * Time: 15:50
 */ 
class FactoryX_AjaxLogin_Helper_Data extends Mage_Core_Helper_Abstract {

    protected $logFileName = 'factoryx_ajaxlogin.log';

    /**
     * Log data
     * @param string|object|array data to log
     */
    public function log($data)
    {
        Mage::log($data, null, $this->logFileName);
    }

}