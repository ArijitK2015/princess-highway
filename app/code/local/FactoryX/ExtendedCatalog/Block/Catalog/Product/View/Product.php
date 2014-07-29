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
				'cache_key' => $this->makeCacheKey()
			)
		);
	}
	
	public function getCacheTags() 
	{
		return array(Mage_Catalog_Model_Product::CACHE_TAG);
	}
		
	public function getCacheKey() 
	{
		if (!$this->hasData('cache_key')) 
		{
			$cacheKey = $this->makeCacheKey();
			$this->setCacheKey($cacheKey);
		}
		return $this->getData('cache_key');
	}	
		
	private function makeCacheKey() 
	{
		// Product ID
		$prodId = $this->getRequest()->getRequestUri();
		// Generate cache key based on store ID, theme and product ID
		$cacheKey = sprintf("PRODUCT_%d_%s_%s", Mage::app()->getStore()->getId(), Mage::getSingleton('core/design_package')->getPackageName(), $prodId);

		return $cacheKey;
	}
		
}
