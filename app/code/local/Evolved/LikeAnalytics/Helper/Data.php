<?php

/**
 * Retail Evolved - Facebook Like Analytics
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA that is bundled with this
 * package in the file EVOLVED_EULA.txt.
 * It is also available through the world-wide-web at this URL:
 * http://retailevolved.com/eula-1-0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to service@retailevolved.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * You may edit this file, but only at your own risk, as long as it is within
 * the constraints of the license agreement. Before upgrading the module (not Magento), 
 * be sure to back up your original installation as the upgrade may override your
 * changes.
 *
 * @category   Evolved
 * @package    Evolved_LikeAnalytics
 * @copyright  Copyright (c) 2010 Kaelex Inc. DBA Retail Evolved (http://retailevolved.com)
 * @license    http://retailevolved.com/eula-1-0 (Retail Evolved EULA 1.0)
 */

class Evolved_LikeAnalytics_Helper_Data extends Mage_Core_Helper_Abstract
{
	//const API_BASE = 'http://api.facebook.com/restserver.php?method=links.getStats&urls=';
	
	const API_BASE = 'http://api.facebook.com/method/fql.multiquery?queries=%7B';
	
	private $_storeUrls = array();
	private $_storeConfig = array();
	private $_analyticsConfig = array();
	
	public function getFlag() {
		$flag = Mage::getModel('evlikeanalytics/stat_flag')->loadSelf();
		
		return $flag;
	}
	
	public function getKillFlag() {
		$killFlag =  Mage::getModel('evlikeanalytics/stat_kill_flag')->loadSelf();
		
		return $killFlag;
	}
	
	protected function _getBaseUrl($store)
    {
		$storeId = $store->getId();
		if(!isset($_storeUrls[$storeId])) {
			$_storeUrls[$storeId] = $store->getBaseUrl('web', false);
		}
		
        return $_storeUrls[$storeId];
    }

	protected function _getStoreConfig($path, $storeId) {
		if(!isset($_storeConfig[$storeId])) {
			$_storeConfig[$storeId] = array();
		}
		
		if(!isset($_storeConfig[$storeId][$path])) {
			$_storeConfig[$storeId][$path] = Mage::getStoreConfig($path, $storeId);
		}
		
		return $_storeConfig[$storeId][$path];
	}
	
	protected function _getProductUrlSuffix() {
		if(!$this->getData('product_url_suffix')) {
			$this->setData('product_url_suffix', Mage::helper('catalog/product')->getProductUrlSuffix());
		}
		
		return $this->getProductUrlSuffix();
	}
	
	public function getProductUrls($product, $store) {
		$_storeId = $store->getId();

		$_url = $this->_getBaseUrl($store);

		if(!Mage::getStoreConfig('web/seo/use_rewrites', $_storeId)) {
			$_url .= 'index.php/';
		} 
		
		if (Mage::getStoreConfig('web/url/use_store', $_storeId) && $storeCode = $store->getCode()) {
			$_url .= $storeCode . '/';
		}
		
		$_urls = array();
		
		// Check if URL rewrite is available
		$rewrite = Mage::getModel('core/url_rewrite');
		
		if ($product->getStoreId()) {
               $rewrite->setStoreId($product->getStoreId());
           }
           else {
               $rewrite->setStoreId($store->getId());
          	}

		$idPath = 'product/'.$product->getId();

		$rewrite->loadByIdPath($idPath);
		
		if ($rewrite->getId()) {
			$_urls[] = $_url . $rewrite->getRequestPath();
			
			$_categoryIds = $product->getCategoryIds();
			
			$_categoryCount = count($_categoryIds);
			
			for($i = 0; $i < $_categoryCount; $i++) {
				$idPath = 'product/' . $product->getId() . '/' . $_categoryIds[$i];
			}
			
			$rewrite->loadByIdPath($idPath);
			
			if($rewrite->getId()) {
				$_urls[] = $_url . $rewrite->getRequestPath();
			}
           }

		if(count($_urls)) 
			return $_urls;

		$_url .= $product->getUrlKey() . $this->_getProductUrlSuffix();
		
		return array($_url);
	}
	
	public function getCategoryUrl($category, $store) {
		$_storeId = $store->getId();
		$_url = $this->_getBaseUrl($store);

		if(!Mage::getStoreConfig('web/seo/use_rewrites', $_storeId)) {
			$_url .= 'index.php/';
		}
		
		if (Mage::getStoreConfig('web/url/use_store', $_storeId) && $storeCode = $store->getCode()) {
			$_url .= $storeCode . '/';
		}
		
		// Check if URL rewrite is available
		$rewrite = Mage::getModel('core/url_rewrite');
		
		if ($category->getStoreId()) {
               $rewrite->setStoreId($category->getStoreId());
           }
           else {
               $rewrite->setStoreId($store->getId());
           }

		$idPath = 'category/'.$category->getId();
		
		$rewrite->loadByIdPath($idPath);
		
		if ($rewrite->getId()) {
			$_url .= $rewrite->getRequestPath();
			return $_url;
        }
		
		$_url .= $category->getUrlPath() . Mage::helper('catalog/category')->getCategoryUrlSuffix();
		
		return $_url;
	}
	
	public function getStats($urls) {
		// Build the request
		$counter = count($urls);
		
		$requestUrl = self::API_BASE;
		
		for($i = 0; $i < $counter; $i++) {
			//$requestUrl .= "\"link_stat" . $i . "\":" . "\"select like_count, share_count, comment_count, total_count, click_count from link_stat where url='" . $urls[$i] . "'\",";
			$requestUrl .= "%22link_stat" . $i . "%22%3A" . "%22select%20like_count,%20share_count,%20comment_count,%20total_count,%20click_count%20from%20link_stat%20where%20url='" . $urls[$i] . "'%22,";
		}
		
		$requestUrl .= "%7D";
		
		Mage::log($requestUrl);
		$request = new Zend_Http_Client($requestUrl, array(
			'maxredirects' => 0,
			'timeout' => 30));
		$r = $request->request();

		$xmlReader = new XMLReader();
		$xmlReader->XML($r->getBody());
		
		$_stats = array();
		$finishedParsing = false;
		
		$counter = 0;
		
		while(!$finishedParsing && $xmlReader->read())
		{
			switch($xmlReader->nodeType)
			{
				case (XMLREADER::ELEMENT):
					if (substr($xmlReader->name, 0, 9) === "link_stat") {
						$object = Mage::getModel("evlikeanalytics/stat");
						$object->loadXml($xmlReader);
						$object->setUrl($urls[$counter++]);
						$_stats[] = $object;
					}
					break;
				case (XMLREADER::END_ELEMENT):
					if($xmlReader->name == "links_getStats_response") {
						$xmlReader->read();
						$finishedParsing = true;
					}
					break;
			}
		}
		
		return $_stats;
	}
	
}