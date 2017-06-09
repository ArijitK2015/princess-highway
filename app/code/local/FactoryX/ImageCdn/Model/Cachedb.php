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

class FactoryX_ImageCdn_Model_Cachedb extends Mage_Core_Model_Abstract
{
    protected function _construct() {
		parent::_construct();
        $this->_init('imagecdn/cachedb', 'cachedb_id');
    }

    /**
     * @return bool
     */
    public function reupload(){
        $uri = parse_url($this->getUrl());
        $path = $uri['path'];
        $success = true;
        if (Mage::getStoreConfig('imagecdn/general/status') == 'imagecdn/adapter_amazons3'){
            $apiKey = Mage::getStoreConfig('imagecdn/amazons3/access_key_id');
            $apiSecret = Mage::getStoreConfig('imagecdn/amazons3/secret_access_key');
            $bucket = Mage::getStoreConfig('imagecdn/amazons3/bucket');

            // Does this file already exists on S3?
            if (Mage::helper('imagecdn')->isImageExistOnS3($path)){
                $this->setData('http_code','200');
                $this->save();
                return $success;
            }

            // Does this file exists locally?
            $this->generateLocalCache($path);
            try{
                $client = new FactoryX_ImageCdn_Model_Adapter_Amazons3_Wrapper(array('key'=>$apiKey,'private_key'=>$apiSecret));
                if ($client->uploadFile($bucket,substr($path,1),Mage::getBaseDir('media').$path,true)){
                    $this->setData('http_code','200');
                }
            } catch (Exception $e){
                $this->setData('http_code','500');
                $success = false;
            }
            $this->setData('last_upload',date("Y-m-d H:i:s"));
            $this->save();
        }
        return $success;
    }

    /**
     * @param $path
     * @return bool
     */
    public function generateLocalCache($path){
        if (file_exists(Mage::getBaseDir('media').$path)){
            return true;
        }
        $elements = explode('/',$path);

        if (sizeof($elements) == 4){
            // Category Image
            $width  = '';
            $height = '';
            unset($elements[0]); //empty
        }elseif (sizeof($elements) == 10) {
            // Original Product Image
            $width  = '';
            $height = '';
            //unset($elements[9]); //filename
            //unset($elements[8]); //secondletter
            //unset($elements[7]); //firstletter
            unset($elements[6]); //randomstring
            unset($elements[5]); //image
            unset($elements[4]); //1
            unset($elements[3]); //cache
            //unset($elements[2]); //product
            //unset($elements[1]); //catalog
            unset($elements[0]); //empty
        }else{
            // Reized Product Image
            $dimension = explode('x',$elements[6]);
            if (count($dimension) == 2){
                if ($dimension[1]) {
                    $width = $dimension[0];
                    $height = $dimension[1];
                } else {
                    $width = $dimension[0];
                    $height = $dimension[0];
                }
            }
            //unset($elements[10]); //filename
            //unset($elements[9]); //secondletter
            //unset($elements[8]); //firstletter
            unset($elements[7]); //randomstring
            unset($elements[6]); //230x260
            unset($elements[5]); //small_image
            unset($elements[4]); //1
            unset($elements[3]); //cache
            //unset($elements[2]); //product
            //unset($elements[1]); //catalog
            unset($elements[0]); //empty
        }

        $source_path = Mage::getBaseDir('media').DS.implode('/',$elements);

        Mage::helper('imagecdn')->resizeImg($source_path,$path,$width,$height);
    }
}