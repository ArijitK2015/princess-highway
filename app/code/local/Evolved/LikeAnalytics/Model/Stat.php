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

class Evolved_LikeAnalytics_Model_Stat extends Mage_Core_Model_Abstract
{
	/**
     * Retreive store collection
     *
     * @return array
     */
    protected function _getStores()
    {
        $stores = $this->getData('_stores');
        if (is_null($stores)) {
            $stores = Mage::app()->getStores();
            $this->setData('_stores', $stores);
        }
        return $stores;
    }

	/**
     * Return collection with product and store filters
     *
     * @param Mage_Core_Model_Store $store
     * @param mixed $products
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    protected function _getProductCollection($store)
    {
				$visibility = array(
					Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
					Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
				);
				
        $collection = Mage::getModel('catalog/product')
            ->getCollection()
						->addAttributeToFilter('visibility', $visibility)
						->addAttributeToSelect('name')
            ->setStoreId($store)
            ->addStoreFilter($store);

        return $collection;
    }

	protected function _getCategoryCollection($store)
	{
		$collection = Mage::getModel('catalog/category')->getCollection()
			->addAttributeToSelect('name');

     /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */
     $collection->setProductStoreId($store->getId())
         ->setStoreId($store->getId());

		return $collection;
	}
	
	protected function _mergeUrls(&$_newUrls, &$_urls, &$_statHash, $_object) {
		$_urlCount = $_newUrls ? count($_newUrls) : 0;
		
		for($i = 0; $i < $_urlCount; $i++) {
			$_url = $_newUrls[$i];
			$stat = Mage::getModel('evlikeanalytics/stat');
			$stat->load($_url, 'url');
			$stat->setUrl($_url);
			$stat->setDescription($_object->getName());
			$_statHash[$_url] = $stat;
		}
		
		if($_newUrls)
			$_urls = array_merge($_urls, $_newUrls);
	}
	
	protected function _getWebStats(&$_urls, &$_statHash, $_minUrlCount) 
	{
		if(!$_urls || !$_statHash)
			return;
			
		if(count($_urls) > $_minUrlCount) {
			// Go get the stats from the web and merge them together with the prepopulated stats
			$_webStats = Mage::helper('evlikeanalytics')->getStats($_urls);
			
			// Loop through web stats and assign values to the saved stats. Save the stat and move on.
			$_webStatCount = $_webStats ? count($_webStats) : 0;
			
			for($i = 0; $i < $_webStatCount; $i++) {
				$_tempStat = $_webStats[$i];

				$_permStat = $_statHash[$_tempStat->getUrl()];
				$_permStat->setShareCount($_tempStat->getShareCount());
				$_permStat->setLikeCount($_tempStat->getLikeCount());
				$_permStat->setCommentCount($_tempStat->getCommentCount());
				$_permStat->setTotalCount($_tempStat->getTotalCount());
				$_permStat->setClickCount($_tempStat->getClickCount());
				$_permStat->setUpdateTime(date('Y-m-d H:i:s'));
				
				$_permStat->save();
			}
			
			$_urls = array();
			$_statHash = array();
		}
	}

	public function _construct()
	{
		parent::_construct();
		$this->_init('evlikeanalytics/stat');
	}
	
	public function generate($force = false) {
		/**
         * Check indexer flag
         */
        $flag = Mage::helper('evlikeanalytics')->getFlag();

        if ($flag->getState() == Evolved_LikeAnalytics_Model_Stat_Flag::STATE_RUNNING) {
			if($force) {
				$kill = Mage::helper('evlikeanalytics')->getKillFlag();
                $kill->setFlagData($flag->getFlagData())->save();
				$flag->setState(Mage_CatalogIndex_Model_Catalog_Index_Flag::STATE_QUEUED)->save();
			} else {
            	return $this;
			}
        } else /*if ($flag->getState() == Mage_CatalogIndex_Model_Catalog_Index_Flag::STATE_QUEUED)*/ {
            $flag->setState(Evolved_LikeAnalytics_Model_Stat_Flag::STATE_RUNNING)->save();
        }

		try {
			// Delete the current stats
			$this->getResource()->deleteAll();
			
			// Get all stores
			$stores = $this->_getStores();
			
			foreach($stores as $store) {
				// Get all products
				$collection = $this->_getProductCollection($store);
				
				$_statHash = array();
				$_urls = array();
				
				foreach($collection as $product) {
										
					// Get product URLs
					$_tempUrls = Mage::helper('evlikeanalytics')->getProductUrls($product, $store);

 					$this->_mergeUrls($_tempUrls, $_urls, $_statHash, $product);
					
					$this->_getWebStats($_urls, $_statHash, 9);
				}
				
				// Get all categories
				$collection = $this->_getCategoryCollection($store);
				
				foreach($collection as $category) {
					
					// Get category URL
					$_url = array(Mage::helper('evlikeanalytics')->getCategoryUrl($category, $store));
					
					$this->_mergeUrls($_url, $_urls, $_statHash, $category);
					
					$this->_getWebStats($_urls, $_statHash, 9);
				}
				
				// Run get web stats with a min of 0 to clear out any left over URLs
				$this->_getWebStats($_urls, $_statHash, 0);
			}
		} catch (Exception $e) {
			$flag->delete();
			throw $e;
		}
		
		if ($flag->getState() == Evolved_LikeAnalytics_Model_Stat_Flag::STATE_RUNNING) {
            $flag->delete();
        }

		return $this;
	}
	
	public function loadXml($xmlReader) 
	{	
		$finishedParsing = false;
		
		while(!$finishedParsing && $xmlReader->read())
		{
			switch($xmlReader->nodeType)
			{
				case (XMLREADER::ELEMENT):
					$name = $xmlReader->name;
					$xmlReader->read();
					$this->setData($name, $xmlReader->value);
					break;
				case (XMLREADER::END_ELEMENT):
					if($xmlReader->name == "link_stat") {
						$xmlReader->read();
						$finishedParsing = true;
					}
					break;
			}
		}
	}
}