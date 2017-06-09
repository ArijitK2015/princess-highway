<?php

/**
 * Class FactoryX_ImageCdn_IndexController
 */

class FactoryX_ImageCdn_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * @return bool
     */
    public function logAction(){
        $url_base        = Mage::getStoreConfig('imagecdn/amazons3/url_base');
        $url_base_secure = Mage::getStoreConfig('imagecdn/amazons3/url_base_secure');
        $url             = Mage::app()->getRequest()->getParam('url');
        $url_referrer    = Mage::app()->getRequest()->getParam('url_referrer');

        $response['status'] = false;

        if ($url_base && $url_base_secure && $url)
        {
            if ((strpos($url,$url_base) !== false) || (strpos($url,$url_base_secure) !== false)){
                $response['status'] = Mage::helper('imagecdn')->logFaultyUrl($url, $url_referrer);
            }

            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        }
        else return false;
    }
}