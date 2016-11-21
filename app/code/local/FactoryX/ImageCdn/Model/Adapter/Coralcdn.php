<?php
/**
 * FactoryX_ImageCdn
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0), a
 * copy of which is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FactoryX
 * @package    FactoryX_ImageCdn
 * @author     FactoryX Codemaster <codemaster@factoryx.com>
 * @copyright  Copyright (c) 2009 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */


/**
 * CDN adapter for Coral CDN.
 * All the methods in the class are not used since Coral is am on-demand service.
 */
class FactoryX_ImageCdn_Model_Adapter_CoralCdn extends FactoryX_ImageCdn_Model_Adapter_Abstract
{
	/**
	 * @param $relFilename
	 * @param string $tempfile
	 * @return bool
     */
	public function save($relFilename, $tempfile) {
        return false;
    }

	/**
	 * @param string $relFilename
	 * @param string $tempfile
	 * @return bool
     */
	protected function _save($relFilename, $tempfile) {
        return false;    	
    }

	/**
	 * @param $relFilename
	 * @return bool
     */
	public function remove($relFilename) {
    	return false;
    }

	/**
	 * @param string $relFilename
	 * @return bool
     */
	protected function _remove($relFilename) {
    	return false;
    }

	/**
	 * @return bool
     */
	public function clearCache() {
    	return true;
    }

	/**
	 * @return bool
     */
	protected function _clearCache() {
    	return true;
    }

	/**
	 * @param $filename
	 * @return bool
     */
	public function fileExists($filename) {
    	return file_exists($filename);
    }

	/**
	 * Creates a full URL to the image on the remote server
	 *
	 * @param $filename
	 * @return string
	 * @internal param string $relFilename path (with filename) from the CDN root
	 */
    public function getUrl($filename) {
	    $filename = '/media' . $this->getRelative($filename);
	    $var = Mage::app()->getStore()->isCurrentlySecure() ? 'imagecdn/coralcdn/url_base_secure' : 'imagecdn/coralcdn/url_base';
	    return Mage::getStoreConfig($var) . $filename;  
    }
	
	/**
	 * If there is no secure base URL do not use the CDN to serve images
	 * 
	 * @return bool
	 */
	public function useCdn() {
    	if(Mage::app()->getStore()->isCurrentlySecure()) {
    		$url_base_secure = Mage::getStoreConfig('imagecdn/coralcdn/url_base_secure');
    		if(empty($url_base_secure)) {
    			return false;
    		}
    	}
		return parent::useCdn();
	}

	/**
	 * @return bool
     */
	protected function _onConfigChange() {
		return true;
	}
    
}