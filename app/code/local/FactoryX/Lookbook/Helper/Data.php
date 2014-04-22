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
	
	public function calculateLookDimensions($lookbook)
	{
		$lookbookType = $lookbook->getLookbookType();
		if ($lookbookType == "category")
		{
			// Get first lookbook product
			$_firstProduct = $lookbook->getLookbookProducts()->getFirstItem();
			
			// Load product
			$_loadedProduct = Mage::getModel('catalog/product')->load($_firstProduct->getEntityId());
			
			// Get image without using the cache (as we need the original size)
			$_image = Mage::getSingleton('catalog/product_media_config')->getBaseMediaUrl().$_loadedProduct->getImage();
			
			// Retrieve attributes of the image
			list($width, $height) = getimagesize($_image);
			
			$dimensions['width'] = $width;
			$dimensions['height'] = $height;
			$dimensions['ratio'] = $height / $width;
		}
		else
		{
			// Get lookbook images
			$_images = $lookbook->getGallery();
			
			// Get first image
			$_firstImage = $_images[0];
			
			// Generate its real path
			$imagePath = sprintf("%s/lookbook%s", Mage::getBaseDir('media'), $_firstImage['file']);
			
			// Retrieve attributes of the image
			list($width, $height) = getimagesize($imagePath);
			
			$dimensions['width'] = $width;
			$dimensions['height'] = $height;
			$dimensions['ratio'] = $height / $width;
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
			$childProduct = Mage::getModel('catalog/product')->load(reset($parentId));
			
			// Product enabled + product visible + product configurable = product link
			if ($childProduct->getStatus() == 1 && $childProduct->getVisibility() == Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
			{
				$childProductsLinks[] = array( 'link' => $childProduct->getProductUrl(), 'name' => $childProduct->getName(), 'price' =>  Mage::helper('core')->currency($childProduct->getPrice(), true, false));
			}
		}
		
		return $childProductsLinks;
	}

}