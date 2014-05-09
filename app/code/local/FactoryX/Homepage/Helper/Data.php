<?php

class FactoryX_Homepage_Helper_Data extends Mage_Core_Helper_Abstract
{

	protected $logFileName = 'factoryx_homepage.log';
	
	/**
	 *	Getter for the current homepage
	 */
	public function getCurrentHomepage($store = null)     
    {
		if ($store == "")	$store = null;
		
		$homepage = Mage::getModel('homepage/homepage')
					->getCollection()
					->addStatusFilter(1)
					->addStoreFilter($store)
					->getFirstItem();
		
		// Ensure the homepage is viewable in the store
		if ($homepage)
		{
			if (!Mage::app()->isSingleStoreMode()) 
			{
				if ($homepage->isStoreViewable()) 
					return $homepage;
				else 
					throw new Exception ('This homepage is not available with this store.');
			}
			else
			{
				return $homepage;
			}
		}
		else return false;
	}
	
	/**
	 * Log data
	 * @param string|object|array data to log
	 */
	public function log($data) 
	{
		Mage::log($data, null, $this->logFileName);
	}
	
	/**
	 *	List files from a directory in an usable way
	 */
	public function dirFiles($directry,$subdir,$slider) 
	{
		// Open Directory
		$dir = dir($directry.DS.$subdir);

		// Declare array
		$filesall = array();
		
		// Reads Directory
		while (false!== ($file = $dir->read())) 
		{
			// Gets the File Extension and the Layout Name
			list($layoutName,$extension) = explode('.', $file, 2);
			// Extensions Allowed
			if($extension == "png" || $extension == "jpg" || $extension == "gif" |$extension == "jpeg") 
			{
				// If no slider / If slider
				if ((!$slider && strpos($file,'slider') === false) || ($slider && strpos($file,'slider') !== false))
				{
					// Store in Array
					$filesall[] = array('value' => $subdir.DS.$layoutName,'image' => $subdir.DS.$layoutName.'.'.$extension); 
				}
			}
		}
		
		// Close Directory
		$dir->close(); 
		// Sorts the Array
		sort($filesall);
		// Return the Array
		return $filesall;
	}
	
	/**
	 *	Add all images data to the homepage data
	 */
	public function addImagesData($data)
	{
		if (array_key_exists('homepage_id',$data) && array_key_exists('amount',$data))
		{
			// Retrieve the homepage pictures
			$homepagePictures = Mage::getModel('homepage/image')->getCollection()->addHomepageFilter($data['homepage_id']);
			// Foreach pictures
			foreach($homepagePictures as $homepagePicture)
			{
				// Retrieve data
				$data['image_'.$homepagePicture->getIndex()] = $homepagePicture->getUrl();
				$data['link_'.$homepagePicture->getIndex()] = $homepagePicture->getLink();
				$data['alt_'.$homepagePicture->getIndex()] = $homepagePicture->getAlt();
				$data['popup_'.$homepagePicture->getIndex()] = $homepagePicture->getPopup();
			}
			return $data;
		}
		else return $data;
	}
	
	/*
	 *
	 */
	public function isHomepageModuleUsed($store = null)
	{
		return Mage::getStoreConfig('homepage/options/enable', $store);
	}

}