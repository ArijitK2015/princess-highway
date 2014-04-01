<?php

class FactoryX_Homepage_Model_Homepage extends Mage_Core_Model_Abstract 
{
	const CACHE_TAG	= 'homepage_homepage';

	/**
	 *	Constructor for the homepage model
	 */
    protected function _construct()
    {
        $this->_init('homepage/homepage', 'homepage_id');
    }
	
	/**
	 *	Check if viewable in store
	 */
	public function isStoreViewable()
	{
		$homepages = Mage::getResourceModel('homepage/homepage_collection')
							->addIdFilter($this->getHomepageId())
							->addStoreFilter();
		
		if (count($homepages) == 1) 
			return true;
		else 
			return false;
	}
	
	/**
	 *	Getter for an homepage image based on its index
	 */
	public function getImage($index)
	{
		$homepageImages = Mage::getModel('homepage/image')
							->getCollection()
							->addHomepageFilter($this->getHomepageId())
							->addIndexFilter($index);
							
		if (count($homepageImages)==1) return $homepageImages->getFirstItem();
		else return false;
	}
	
	/**
	 *	Getter for several homepage images based on an array of indexes
	 */
	public function getImages($indexes)
	{
		$homepageImages = Mage::getModel('homepage/image')
							->getCollection()
							->addHomepageFilter($this->getHomepageId())
							->addIndexFilter($indexes);
							
		if (count($homepageImages)) return $homepageImages;
		else return false;
	}
	
	/**
	 *	Getter for all images of an homepage
	 */
	public function getAllImages()
	{
		$homepageImages = Mage::getModel('homepage/image')
							->getCollection()
							->addHomepageFilter($this->getHomepageId());
							
		return $homepageImages;
	}
}