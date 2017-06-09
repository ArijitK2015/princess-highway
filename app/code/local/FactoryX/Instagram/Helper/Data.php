<?php
/**
 * iKantam
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade InstagramConnect to newer
 * versions in the future.
 *
 * @category    Ikantam
 * @package     FactoryX_Instagram
 * @copyright   Copyright (c) 2012 iKantam LLC (http://www.factoryx.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class FactoryX_Instagram_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $logFileName = 'factoryx_instagram.log';

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
        return Mage::getStoreConfigFlag('factoryx_instagram/module_options/enabled');
    }

    /**
     * @return mixed
     */
    public function isLogEnabled()
    {
        return Mage::getStoreConfigFlag('factoryx_instagram/debug/enable_log');
    }

    /**
     * @return mixed
     */
    public function showImagesOnProductPage()
    {
        return Mage::getStoreConfigFlag('factoryx_instagram/product_options/product');
    }

    /**
     * @return mixed
     */
    public function getProductPageLimit()
    {
        return Mage::getStoreConfigFlag('factoryx_instagram/product_options/product_limit');
    }

    /**
     * To request multiple scopes at once, simply separate the scopes by a space
     *
     * @return mixed
     */
    public function getScopes()
    {
        $scopes = Mage::getStoreConfig('factoryx_instagram/module_options/client_permission');
        $scopes = preg_replace("/,/", " ", $scopes);
        $this->log(sprintf("%s->scopes: %s", __METHOD__, $scopes));
        return $scopes;
    }

    /**
     * Build url
     * @param $url string Ex. 'http://example.com'
     * @param $params array Ex. array( 'page' => 1 )
     * @return string Ex. 'http://example.com?page=1'
     */
    public function buildUrl($url, $params){

        $strParams = array();
        foreach($params as $key => $value){
            $strParams[] = $key . '=' . urlencode($value);
        }
        $buildedUrl = is_null($url) ? '' : $url . '?';
        return $buildedUrl . implode('&', $strParams);
    }

    /**
     * Get module config section url in admin configuration
     * @return string
     */
    public function getAdminConfigSectionUrl()
    {
        $url = Mage::getModel('adminhtml/url');
        return $url->getUrl('adminhtml/system_config/edit', array(
            '_current'  => true,
            'section'   => 'factoryx_instagram'
        ));
    }

    /**
     * Clean up the hash tag to ensure there is a space in front of # character
     * (except when it's the start of the caption)
     * @param $caption
     * @return mixed
     */
    public function cleanHashtag($caption){
        $result = $caption;
        $position = 0;
        $offset_original = 0;
        while ($position = strpos($caption,'#',$position)){
            if ($position != 0){
                if ($caption[$position-1] != " "){
                    $result = substr_replace($result, " ", ($position+$offset_original), 0);
                    $offset_original++;
                }
            }
            if ($position < (strlen($caption) - 1)){
                $position++;
            }else{
                break;
            }
        }
        return $result;
    }

    public function isConfigurableSkusjQuery()
    {
        return (string)Mage::getConfig()->getModuleConfig('FactoryX_ConfigurableSkus')->active ? null : "lib/factoryx/instagram/jquery-1.10.2.min.js";
    }

    public function isConfigurableSkusNoConflict()
    {
        return (string)Mage::getConfig()->getModuleConfig('FactoryX_ConfigurableSkus')->active ?  null : "lib/factoryx/instagram/noconflict.js";
    }

    public function logImport($image,$listId)
    {
        $imageId = $image->getId();
        $log = Mage::getModel('instagram/instagramlog')->loadByIds($imageId,$listId);
        if (!$log)
        {
            $log = Mage::getModel('instagram/instagramlog')->setImageId($imageId)->setListId($listId)->save();
        }
        return $log->getId();
    }
}
