<?php
/**
 * Add caching and reviews to category list
 */
class FactoryX_ExtendedCatalog_Block_Catalog_Product_List extends Mage_Catalog_Block_Product_List {
	
	protected function _construct()	
	{
		$this->addData(
			array(
				'cache_lifetime'    => 86400,
				'cache_key'         => $this->makeCacheKey()
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

	/**
	 *	Creates a cache_key
	 *	<prefix>_STORE_1_DEFAULT__CATALOG_CATEGORY_VIEW_ID_6
	 */
	private function makeCacheKey() 
	{		
		// Category ID
		$catId = $this->getRequest()->getRequestUri();
        $arr = parse_url($catId);
        $catId = $arr['path'];

        // Device
		$device = "desktop";
		if (isset($_SERVER['HTTP_USER_AGENT']) && stripos($_SERVER['HTTP_USER_AGENT'], 'ipad')) {
			$device = "ipad";
		}
		$catId .= sprintf("_%s", $device);
    	
		$qs = false;
        if (array_key_exists('query', $arr)) {
			// Cut query
	        parse_str($arr['query'], $arr);
	        
	        $query = array();
	        $allowedKeys = array(
				// toolbar
				'dir','order','limit','q','p','mode',
				// add attributes
				'cat','brand','colour_description','dress_size','accessories_size','size_mens_28_to_36','size_shoes_girls_36_to_41','size_smlxl','top_size','price'
			);
	        foreach($allowedKeys as $key) {
	            if (array_key_exists($key, $arr)) {
					$query[$key] = $arr[$key];
	            }
	        }
	        if (count($query)) {
	            $qs = http_build_query($query);
	            $catId .= "?" . $qs;
	        }
		}
		elseif(is_array($params = $this->getRequest()->getParams())) {
			
			$query = array();
			$allowedKeys = array(
				// toolbar
				'dir','order','limit','q','p','mode',
				// add attributes
				'cat','brand','colour_description','dress_size','accessories_size','size_mens_28_to_36','size_shoes_girls_36_to_41','size_smlxl','top_size','price'
			);
			foreach($allowedKeys as $key) {
				if (array_key_exists($key, $params)) {
					$query[$key] = $params[$key];
				}
			}
			if (count($query)) {
				$qs = http_build_query($query);
				$catId .= "?" . $qs;
			}
		}
		
		// Check for currency cookies (yum)
		$currency = Mage::getModel('core/cookie')->get("currency");
		if ($currency) {
			$catId .= sprintf("%scurrency=%s", ($qs)?"&":"?", $currency);
		}
		        
        // Generate the cache key
        $cacheKey = sprintf("STORE_%d_%s_%s", Mage::app()->getStore()->getId(), Mage::getSingleton('core/design_package')->getPackageName(), $catId);
        
        return $cacheKey;
	}
	
	/**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     */
    protected function _beforeToHtml()
    {
        /*$toolbar = $this->getLayout()->createBlock('catalog/product_list_toolbar', microtime());
        if ($toolbarTemplate = $this->getToolbarTemplate()) {
            $toolbar->setTemplate($toolbarTemplate);
        }*/
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->_getProductCollection();

        // use sortable parameters
        if ($orders = $this->getAvailableOrders()) {
            $toolbar->setAvailableOrders($orders);
        }
        if ($sort = $this->getSortBy()) {
            $toolbar->setDefaultOrder($sort);
        }
        if ($dir = $this->getDefaultDirection()) {
            $toolbar->setDefaultDirection($dir);
        }
        if ($modes = $this->getModes()) {
            $toolbar->setModes($modes);
        }

        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);

        $this->setChild('toolbar', $toolbar);
        Mage::dispatchEvent('catalog_block_product_list_collection', array(
            'collection' => $this->_getProductCollection()
        ));

        $this->_getProductCollection()->load();
		// Add the reviews to the catalog list
        Mage::getModel('review/review')->appendSummary($this->_getProductCollection());
        return Mage_Catalog_Block_Product_Abstract::_beforeToHtml();
    }
}
