<?php

class FactoryX_Lookbook_Helper_Data extends Mage_Core_Helper_Abstract
{

	protected $logFileName = 'factoryx_lookbook.log';
	
	/**
	 * Log data
	 * @param string|object|array data to log
	 */
	public function log($data) 
	{
		Mage::log($data, null, $this->logFileName);
	}
	
    /**
     * calculateLookDimensions
     *
     * calculates the look dimensions
     *
     * @param FactoryX_Lookbook_Model_Lookbook $lookbook the lookbook
     * @return array $dimensions array('width' => width, 'height' => height)
     */     
	public function calculateLookDimensions($lookbook) {
	
		$lookbookType = $lookbook->getLookbookType();	
		if ($lookbookType == "category") 
		{
			if ($lookbookProducts = $lookbook->getLookbookProducts())
			{
				// Get first lookbook product
				$_firstProduct = $lookbookProducts->getFirstItem();
				// Get image without using the cache (as we need the original size)
				$_image = sprintf("%s/catalog/product/%s", Mage::getBaseDir('media'), $_firstProduct->getImage() );
				
				// Retrieve attributes of the image (may not exist)
				try {
					list($width, $height) = getimagesize($_image);
				}
				catch(Exception $ex) {
					// TODO: use dev default
					$width = 0;
					$height = 0;
				}
				$dimensions['width'] = $width;
				$dimensions['height'] = $height;
				$dimensions['ratio'] = 0;
				if ($height != 0 && $width != 0) {
					$dimensions['ratio'] = $height / $width;
				}
			}
			else {
				$dimensions['ratio'] = 0;
				$dimensions['width'] = 0;
				$dimensions['height'] = 0;
				return $dimensions;
			}
		}
		else {
			// Get lookbook images
			$_images = $lookbook->getGallery();
			// Get first image
			$_firstImage = $_images[0];
			// Generate its real path
			$imagePath = sprintf("%s/lookbook%s", Mage::getBaseDir('media'), $_firstImage['file']);
			// Retrieve attributes of the image
			try {
			    list($width, $height) = getimagesize($imagePath);
			}
			catch(Exception $ex) {
			    // TODO: use dev default
			    $width = 0;
			    $height = 0;
			}
			$dimensions['width'] = $width;
			$dimensions['height'] = $height;
			$dimensions['ratio'] = 0;
			if ($height != 0 && $width != 0) {
			    $dimensions['ratio'] = $height / $width;
			}
		}
		return $dimensions;
	}
	
	/**
	 *	List all categories in an array
	 */
	public function getCategoriesArray() 
	{
		// Get categories
		$categoriesArray = Mage::getModel('catalog/category')
						->getCollection()
						->addAttributeToSelect('name')
						->addAttributeToSort('path', 'asc')
						->load()
						->toArray();
		
		// Make them an usable array
		foreach ($categoriesArray as $categoryId => $category) 
		{
			if (isset($category['name'])) 
			{
				$categories[] = array(
					'label' => $category['name'],
					'level' => $category['level'],
					'value' => $categoryId
				);
			}
		}
		
		return $categories;
	}
	
	public function getChildProductsLink($_product)
	{
		$childProductsLinks = array();
		
		$bundleOptions = $_product->getTypeInstance(true)->getChildrenIds($_product->getId(), false);
		
		foreach ($bundleOptions as $bundleOption)
		{
			// We get the first simple products of the option using reset
			$simpleProductId = reset($bundleOption);
			
			// Retrieve its parent configurable product
			$parentId = Mage::getResourceSingleton('catalog/product_type_configurable')->getParentIdsByChild($simpleProductId); 
			
			// Load the parent			
			$collection = Mage::getResourceModel('catalog/product_collection')
					->addFieldToFilter('entity_id', array(reset($parentId)))
					->addAttributeToSelect(array('status','visibility','product_url','name','price'))
					->setPageSize(1);
					
			$childProduct = $collection->getFirstItem();
			
			// Product enabled + product visible + product configurable = product link
			if ($childProduct->getStatus() == 1 && $childProduct->getVisibility() == Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
			{
				$childProductsLinks[] = array( 'link' => $childProduct->getProductUrl(), 'name' => $childProduct->getName(), 'price' =>  Mage::helper('core')->currency($childProduct->getPrice(), true, false));
			}
		}
		
		return $childProductsLinks;
	}

}