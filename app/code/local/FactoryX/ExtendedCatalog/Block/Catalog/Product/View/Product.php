<?php
/**
 * Product block with caching
 * ab -c 20 -n 100 -X 192.168.100.3:3128 http://shop.alannahhill.com.au/clothing/high-tea-ladies-jacket-season-ss12-from-329.html
*/
class FactoryX_ExtendedCatalog_Block_Catalog_Product_View_Product extends FactoryX_ExtendedCatalog_Block_Catalog_Product_View_Product_Abstract
{
	protected function _construct() 
	{
		$this->addData(
			array(
				'cache_lifetime' => 86400,
				'cache_tags' => $this->getCacheTags()
			)
		);
	}
	
	public function getCacheTags() 
	{
		return array(Mage_Catalog_Model_Product::CACHE_TAG);
	}
		
	public function getCacheKeyInfo() 
	{
		$cacheKey = array();
		
		// ID of data in the cache
		$cacheKey[] = "EXTENDEDCATALOG_PRODUCT";
		// Store ID
		$cacheKey[] = Mage::app()->getStore()->getId();
		// SSL
		$cacheKey[] = (int)Mage::app()->getStore()->isCurrentlySecure();
		// Package
		$cacheKey[] = Mage::getDesign()->getPackageName();
		// Theme
		$cacheKey[] = Mage::getDesign()->getTheme('template');
		// Logged In ?
		$cacheKey[] = Mage::getSingleton('customer/session')->isLoggedIn();
		// Product ID
		$cacheKey[] = $this->getRequest()->getRequestUri();
		
		return $cacheKey;
	}
		
}
