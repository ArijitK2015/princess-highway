<?php

/**
 * Class FactoryX_Homepage_Model_Homepage
 */
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
     *    Getter for an homepage image based on its index
     * @param $index
     * @return bool
     */
	public function getImage($index)
	{
		$homepageImages = Mage::getModel('homepage/image')
							->getCollection()
							->addHomepageFilter($this->getHomepageId())
							->addIndexFilter($index)
							->addNotOverFilter();
							
		if (count($homepageImages)==1) return $homepageImages->getFirstItem();
		else return false;
	}

    /**
     * Getter for an homepage custom css
     * @return string
     */
    public function getCustomCss()
    {
        $customCss = $this->getData('custom_css');
        // condense white space
        $customCss = preg_replace('!\s+!', ' ', $customCss);
        // remove control chars
        $customCss = str_replace(array("\r\n", "\r", "\n", "\t"), '', $customCss);
        return $customCss;
    }

	/**
	 * Getter for an homepage over image based on its index
	 * @param $index
	 * @return bool
	 */
	public function getOverImage($index)
	{
		$homepageImages = Mage::getModel('homepage/image')
			->getCollection()
			->addHomepageFilter($this->getHomepageId())
			->addIndexFilter($index)
			->addOverFilter();

		if (count($homepageImages)==1) return $homepageImages->getFirstItem();
		else return false;
	}

    /**
     *    Getter for several homepage images based on an array of indexes
     * @param $indexes
     * @return bool
     */
	public function getImages($indexes)
	{
		$homepageImages = Mage::getModel('homepage/image')
							->getCollection()
							->addHomepageFilter($this->getHomepageId())
							->addIndexFilter($indexes)
							->addNotOverFilter();
							
		if (count($homepageImages)) return $homepageImages;
		else return false;
	}

	/**
	 *    Getter for several homepage over images based on an array of indexes
	 * @param $indexes
	 * @return bool
	 */
	public function getOverImages($indexes)
	{
		$homepageImages = Mage::getModel('homepage/image')
			->getCollection()
			->addHomepageFilter($this->getHomepageId())
			->addIndexFilter($indexes)
			->addOverFilter();

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
			
			$homepageStoreIds = $readConnection->query($query);
			
			while ($homepageStoreId = $homepageStoreIds->fetch())
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
			$pictureModel->setOver($image->getOver());
			$pictureModel->save();
		}

        return $newHomepage;
    }
}