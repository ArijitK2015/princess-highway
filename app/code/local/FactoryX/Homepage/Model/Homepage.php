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
	
	/**
     * Create duplicate
     *
     * @return FactoryX_Homepage_Model_Homepage
     */
    public function duplicate()
    {
		$data = $this->getData();
		
		// Database resources
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		
		// Check is single store mode
		if (!Mage::app()->isSingleStoreMode()) 
		{
			// Get the homepage related stores
			$query = "SELECT store_id 
						FROM {$resource->getTableName('homepage/store')}
						WHERE homepage_id = {$this->getHomepageId()}";
			
			$homepageStoreIds = $readConnection->fetchAll($query);
			
			foreach ($homepageStoreIds as $homepageStoreId)
			{
				$data['stores'][] = $homepageStoreId['store_id'];
			}
		}
		
        /* @var $newHomepage FactoryX_Homepage_Model_Homepage */
        $newHomepage = Mage::getModel('homepage/homepage')->setData($data)
            ->setStatus(FactoryX_Homepage_Model_Status::STATUS_DISABLED)
            ->setCreatedAt(null)
            ->setEdited(Mage::getModel('core/date')->gmtDate())
            ->setId(null);

        $newHomepage->save();
		
		// Save the images to the database
		foreach ($this->getAllImages() as $image)
		{
			// We duplicate the images
			$pictureModel = Mage::getModel('homepage/image');
			$pictureModel->setUrl($image->getUrl());
			$pictureModel->setLink($image->getLink());
			$pictureModel->setAlt($image->getAlt());
			$pictureModel->setPopup($image->getPopup());
			$pictureModel->setIndex($image->getIndex());
			$pictureModel->setHomepageId($newHomepage->getId());
			$pictureModel->save();
		}

        return $newHomepage;
    }
}