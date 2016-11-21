<?php
/**

 */
class FactoryX_Lookbook_Model_Lookbook extends Mage_Core_Model_Abstract  {

	const CACHE_TAG			= 'FX_LOOKBOOK_CACHE_TAG';
	protected $_cacheTag	= 'FX_LOOKBOOK_CACHE_TAG';

	/**
     * constants for the media gallery
     */
    const BASE_IMAGE = 'image';
    const NO_SELECTION = 'no_selection';

    /**
     * @deprecated since 1.5.9
     */
    const SMALL_IMAGE = 'small_image';
    const THUMBNAIL = 'thumbnail';
    /**
     * end constants for the media gallery
     */

	/**
	 *	Constructor for the homepage model
	 */
    protected function _construct()
    {
        $this->_init('lookbook/lookbook', 'lookbook_id');
    }

	/**
	 *	Check if viewable in store
	 */
	public function isStoreViewable()
	{
		$lookbooks = Mage::getResourceModel('lookbook/lookbook_collection')
							->addIdsFilter($this->getLookbookId())
							->addStoreFilter();

		if (count($lookbooks) == 1)
			return true;
		else
			return false;
	}

	/*
	FactoryX_ImageCdn_Model_Catalog_Category
	*/
    /**
     * @return int
     */
    public function getLookbookProducts() {

		try
		{
            $storeId = Mage::app()->getStore()->getStoreId();
            $category = Mage::getModel('catalog/category')->setStoreId($storeId)->load($this->getCategoryId());

			if (!$category->getId()) 
			{
				throw new Exception("cannot retrieve category '%d' to render, please check store", $this->getCategoryId());
			}

			// Get products from category sorted by position
			$_productCollection = Mage::getResourceModel('catalog/product_collection')->addCategoryFilter($category);
			// Sort by position
			$_productCollection->addAttributeToSort('position', 'asc');
			// Add attributes to the collection
			$_productCollection->addAttributeToSelect(array('name','description'));
			// Add image to the collection
			$_productCollection->joinAttribute('image', 'catalog_product/image', 'entity_id', null, 'left');
			// Add product urls to the collection
			$_productCollection->addUrlRewrite($this->getCategoryId());

			return $_productCollection;
		}
		catch (Exception $e)
		{
			Mage::helper('lookbook')->log($e->getMessage());
			return 0;
		}
	}
	
    /**
     * getLookbookBannerImage
     *
     * get look book banner image
     *
     * @return string banner image url
     */
    public function getLookbookBannerImage() {
        $_bannerImage = "";
		try {
            $storeId = Mage::app()->getStore()->getStoreId();
            $category = Mage::getModel('catalog/category')->setStoreId($storeId)->load($this->getCategoryId());
            //Mage::helper('lookbook')->log(sprintf("%s->category->getId()=%s", __METHOD__, $category->getId()) );
			if ($category->getId()) {
				$_bannerImage = $category->getImageUrl();
			}
		}
		catch (Exception $e) {
			Mage::helper('lookbook')->log($e->getMessage());
		}
		return $_bannerImage;
	}	

	/**
     * Retrieve attributes for media gallery
     *
     * - Needed for media gallery
     *
     * @return array
     */
    public function getMediaAttributes()
    {
        /**
         * In the product media gallery, the media attributes are fetched from
         * the eav attribute table. Attributes with type 'media_image'.
         *
         * A mymodel isn't build with eav attributes, the media types is
         * added as a column in the media table. This function just returns an array.
         */
        $media_attributes = array(self::BASE_IMAGE => 'Base Image');

        return $media_attributes;
    }

    /**
     * Get's all the images linked to a reference and returns them, possible
     * merged with the given array.
     *
     * @param array $images
     * @return string json format
     */
    public function getGallery($images = array())
    {
        if (!is_array($images))
        {
            $images = array();
        }

        $mediaCollection = Mage::getModel('lookbook/lookbook_media')
            ->getCollection()
            ->addFieldToFilter('lookbook_id', $this->getId())
			->addAttributeToSort('position', 'asc');

        foreach ($mediaCollection as $mediaItem)
        {
            $images[] = array(
                'value_id' => $mediaItem->getId(),
                'file' => $mediaItem->getPath(),
                'label' => $mediaItem->getLabel(),
                'position' => $mediaItem->getPosition(),
                'disabled' => $mediaItem->getDisabled(),
            );
        }

        return $images;
    }

}