<?php
/* 
 *	Addto block with caching
 */
class FactoryX_ExtendedCatalog_Block_Catalog_Product_View_Addto extends Mage_Catalog_Block_Product_View_Abstract {

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
		// Based on product ID, store ID and theme
		$prodId = $this->getRequest()->getRequestUri();
		$cacheKey = sprintf("ADDTO_%d_%s_%s", Mage::app()->getStore()->getId(), Mage::getSingleton('core/design_package')->getPackageName(), $prodId);

		return $cacheKey;
	}
	
	/**
     * Check if product can be emailed to friend
     *
     * @return bool
     */
    public function canEmailToFriend()
    {
        $sendToFriendModel = Mage::registry('send_to_friend_model');
        return $sendToFriendModel && $sendToFriendModel->canEmailToFriend();
    }
}
