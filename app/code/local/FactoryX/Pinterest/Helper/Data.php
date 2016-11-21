<?php

class FactoryX_Pinterest_Helper_Data extends Mage_Core_Helper_Abstract {

	/**
	 * @var string
     */
	protected $logFileName = 'factoryx_pinterest.log';

	/**
	 * Log data
	 * @param string|object|array data to log
	 */
	public function log($data)
	{
		Mage::log($data, null, $this->logFileName);
	}

    /**
     * @return mixed
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag('pinterest/general/enable');
    }    

    public function getCustomImage() {
        $customImageSrc = false;
        if (Mage::getStoreConfig('pinterest/general/custom_image')) {
            $customImageSrc = sprintf("%s%s%s%s", Mage::getBaseUrl('media'), "pinterest", DS, Mage::getStoreConfig('pinterest/general/custom_image'));
            $this->log(sprintf("%s->customImageSrc=%s", __METHOD__, $customImageSrc));
        }
        return $customImageSrc;
    }

    /**
     * @return mixed
     */
    public function getCustomImageSize()
    {
        $imgSize = array();
        if (
            Mage::getStoreConfig('pinterest/general/custom_image_size')
            &&
            preg_match("/\d{1,}x\d{1,}/", Mage::getStoreConfig('pinterest/general/custom_image_size'))
        ) {
            $parts = preg_split("/x/", Mage::getStoreConfig('pinterest/general/custom_image_size'));
            $this->log(sprintf("%s->parts[%s]=%s", __METHOD__, Mage::getStoreConfig('pinterest/general/custom_image_size'), print_r($parts, true)));
            $imgSize['w'] = $parts[0];
            $imgSize['h'] = $parts[1];
        }
        return $imgSize;
    }        
    
}