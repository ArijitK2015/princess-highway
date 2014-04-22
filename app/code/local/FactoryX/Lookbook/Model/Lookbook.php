<?php

class FactoryX_Lookbook_Model_Lookbook extends Mage_Core_Model_Abstract 
{
	const CACHE_TAG	= 'lookbook_lookbook';
	
	/**
     * constants for the media gallery
     */
    const BASE_IMAGE = 'image';
    const SMALL_IMAGE = 'small_image';
    const THUMBNAIL = 'thumbnail';
    const NO_SELECTION = 'no_selection';
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
	
	public function getLookbookProducts()
	{
		// Load category based on lookbook category ID
		$category = Mage::getModel('catalog/category')->load($this->getCategoryId());

		// Get products from category sorted by position
		$_productCollection = Mage::getResourceModel('catalog/product_collection')
								->addCategoryFilter($category)
								->addAttributeToSort('position', 'asc');
								
		return $_productCollection;
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
        $media_attributes = array(
            self::BASE_IMAGE => 'Base Image',
            self::SMALL_IMAGE => 'Base Image',
            self::THUMBNAIL => 'Thumbnail',
        );

        return $media_attributes;
    }

    /**
     * Get's all the images linked to a reference and returns them, possible
     * merged with the given array.
     * 
     * @param array $images
     * @return json
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