<?php

/**
 * Class FactoryX_CartImage_Helper_Data
 */
class FactoryX_CartImage_Helper_Data extends Mage_Core_Helper_Abstract
{
	protected $logFileName = 'factoryx_cartimage.log';
    
    public function getSuperAttibutesArray()
    {
        $superAttibutes = Mage::getModel('cartimage/system_config_source_superAttributes')->toOptionArray();
        return $superAttibutes;
    }

    public function isEnabled()
    {
        return Mage::getStoreConfigFlag('cartimage/general/enable');
    }

    public function getCustomAttribute()
    {
        return Mage::getStoreConfig('cartimage/general/custom_attribute');
    }

    public function getCustomImageLabel()
    {
        return Mage::getStoreConfig('cartimage/general/custom_image_label');
    }


	/**
	 * Log data
	 * @param string|object|array data to log
	 */
	public function log($data)
	{
	    /*
	    $backtrace = debug_backtrace();
	    if ($backtrace[1]['function']) {
	        $data = sprintf("%s->%s", $backtrace[1]['function'], $data);
	    }
	    */
		Mage::log($data, null, $this->logFileName);
	}    

}
