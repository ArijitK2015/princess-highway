<?php

class FactoryX_ProductRefresh_Helper_Data extends Mage_Core_Helper_Abstract {

    protected $logFileName = 'factoryx_productrefresh.log';

    /**
     * cleanAttribute
     *
     * replace space and forward slash
     *
     * @param string value attribute value
     * @return string cleaned attribute value
     */
    public function cleanAttribute($val) {
        $val = urldecode($val);
        $val = str_replace(array("\\"), "", $val);
        $val = str_replace(array(" ", "/"), "-", $val);
        return $val;
    }

	/**
	 * Log data
	 * @param string|object|array data to log
	 */
	public function log($data)  {
		Mage::log($data, null, $this->logFileName);
	}

}