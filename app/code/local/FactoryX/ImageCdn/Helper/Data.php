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
 * Helper methods
 */
class FactoryX_ImageCdn_Helper_Data extends Mage_Core_Helper_Abstract
{	
	/**
	 * Factory method for creating the current CDN adapter. Since the adapter class changes
	 * based on the admin config settings, the class can't be hard coded. 
	 *
	 * @return FactoryX_ImageCdn_Model_Adapter_Abstract
	 */
	public function factory() {
		$adapter = Mage::getStoreConfig('imagecdn/general/status');
		if($adapter) {
			return Mage::getSingleton($adapter);
		} else {
			return Mage::getSingleton('imagecdn/adapter_disabled');
		}		
	}

    /**
     * @param $url
     * @param $url_referrer
     * @return bool
     */
    public function logFaultyUrl($url, $url_referrer){
        $cachedb_collection = Mage::getResourceModel('imagecdn/cachedb_collection')->addFieldToFilter('url',$url);
        $_cachedb = Mage::getModel('imagecdn/cachedb');

        if (count($cachedb_collection)){

            foreach ($cachedb_collection as $cachedb){

                $_cachedb->load($cachedb->getId());

                // If this image seems to be already uploaded
                if ($_cachedb->getData('http_code') == "200"){
                    // Do a check
                    $uri = parse_url($url);
                    $path = $uri['path'];
                    if ($this->isImageExistOnS3($path)){
                        // If recheck is good, we return true to trigger the URL chage to refresh the image
                        return true;
                    }
                }

                break;
            }
        }

        // If recheck is not good, we need to mark as bad
        $_cachedb->setData('http_code','403');
        $_cachedb->setData('last_upload',date("Y-m-d H:i:s"));
        $_cachedb->setData('url',$url);
        $_cachedb->setData('url_referrer',$url_referrer);
        $_cachedb->save();

        return false;
    }

    /**
     * @param $path
     * @return bool
     */
    public function isImageExistOnS3($path){
        $apiKey = Mage::getStoreConfig('imagecdn/amazons3/access_key_id');
        $apiSecret = Mage::getStoreConfig('imagecdn/amazons3/secret_access_key');
        $bucket = Mage::getStoreConfig('imagecdn/amazons3/bucket');

        // Does this file already exists on S3?
        try{
            $client = new FactoryX_ImageCdn_Model_Adapter_Amazons3_Wrapper(array('key'=>$apiKey,'private_key'=>$apiSecret));
            if ($client->getObjectInfo($bucket,substr($path,1))){
                return true;
            }
        }catch (Exception $e){}

        return false;
    }

    /**
     * @param $basePath
     * @param $cachePath
     * @param $width
     * @param string $height
     */
    public function resizeImg($basePath, $cachePath, $width, $height = ''){
        $newPath = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $cachePath;
        //if image has already resized then just return URL
        if (file_exists($basePath) && is_file($basePath) && !file_exists($newPath)) {
            $imageObj = new Varien_Image($basePath);
            $imageObj->constrainOnly(TRUE);
            $imageObj->keepAspectRatio(TRUE);
            $imageObj->keepFrame(TRUE);
            $imageObj->backgroundColor(array(255,255,255));
            if ($width){
                $imageObj->resize($width, $height);
            }
            $imageObj->save($newPath);
        }
    }
}