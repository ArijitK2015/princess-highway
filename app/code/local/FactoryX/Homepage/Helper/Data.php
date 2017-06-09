<?php

/**
 * Class FactoryX_Homepage_Helper_Data
 */
class FactoryX_Homepage_Helper_Data extends Mage_Core_Helper_Abstract
{

	protected $logFileName = 'factoryx_homepage.log';
	protected $numberedPaginationStyles = array('b02','b03','b18','b20');

	/**
	 *    Getter for the current homepage
	 * @param null $store
	 * @return bool
	 */
	public function getCurrentHomepages($store = null)
	{
		if ($store == "")	$store = null;

		try
		{

			$homepages = Mage::getModel('homepage/homepage')
				->getCollection()
				->addDisplayedFilter(1)
				->addStoreFilter($store)
				->addAttributeToSort('sort_order');

			// Ensure the homepages are viewable in the store
			if ($homepages)
			{
				if (!Mage::app()->isSingleStoreMode())
				{
					foreach ($homepages as $currentHomepage)
					{
						if ($currentHomepage->isStoreViewable())
							continue;
						else
							throw new Exception ('This homepage is not available with this store.');
					}
					return $homepages;
				}
				else
				{
					return $homepages;
				}
			}
			else return false;
		}
		catch (Exception $e)
		{
			Mage::helper('homepage')->log($this->__('Exception caught in %s under % with message: %s', __FILE__, __FUNCTION__, $e->getMessage()));
			return false;
		}
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
	 *    List files from a directory in an usable way
	 * @param $directry
	 * @param $subdir
	 * @param $slider
	 * @return array
	 */
	public function dirFiles($directry,$subdir,$slider = false)
	{
		// Open Directory
		$dir = dir(str_replace("\\","/",$directry.DS.$subdir));

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
					$filesall[] = array('value' => $subdir."/".$layoutName,'image' => $subdir."/".$layoutName.'.'.$extension);
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
	 *    Add images data to the homepage data
	 * @param $data
	 * @return
	 */
	public function addImagesData($data)
	{
		if (array_key_exists('homepage_id',$data) && array_key_exists('amount',$data))
		{
			// Retrieve the homepage pictures
			$homepagePictures = Mage::getModel('homepage/image')->getCollection()->addHomepageFilter($data['homepage_id'])->addNotOverFilter();
			// Foreach pictures
			foreach($homepagePictures as $homepagePicture)
			{
				$data['type_'.$homepagePicture->getIndex()] = $homepagePicture->getType();
				if ($homepagePicture->getType() == "image")
				{
					// Retrieve data
					$data['image_'.$homepagePicture->getIndex()] = $homepagePicture->getUrl();
					$data['link_'.$homepagePicture->getIndex()] = $homepagePicture->getLink();
					$data['alt_'.$homepagePicture->getIndex()] = $homepagePicture->getAlt();
					$data['popup_'.$homepagePicture->getIndex()] = $homepagePicture->getPopup();
				}
				else
				{
					$data['block_id_'.$homepagePicture->getIndex()] = $homepagePicture->getBlockId();
				}
			}
			return $data;
		}
		else return $data;
	}

	/**
	 *    Add over images data to the homepage data
	 * @param $data
	 * @return
	 */
	public function addOverImagesData($data)
	{
		if (array_key_exists('homepage_id',$data) && array_key_exists('amount',$data))
		{
			// Retrieve the homepage pictures
			$homepagePictures = Mage::getModel('homepage/image')->getCollection()->addHomepageFilter($data['homepage_id'])->addOverFilter();
			// Foreach pictures
			foreach($homepagePictures as $homepagePicture)
			{
				// Retrieve data
				$data['image_over_'.$homepagePicture->getIndex()] = $homepagePicture->getUrl();
			}
			return $data;
		}
		else return $data;
	}

	/**
	 * @param null $store
	 * @return mixed
	 */
	public function isHomepageModuleUsed($store = null)
	{
		return Mage::getStoreConfigFlag('homepage/options/enable', $store);
	}



	/**
	 * @return mixed
	 */
	public function getPosition()
	{
		return Mage::getStoreConfig('homepage/options/position');
	}

	/**
	 * @param $string
	 * @return string
	 */
	public function stripNav($string)
	{
		return str_replace("nav/","",$string);
	}

	/**
	 * @param $string
	 * @return string
	 */
	public function stripPag($string)
	{
		return str_replace("pag/","",$string);
	}

	/**
	 * @param $string
	 * @return boolean
	 */
	public function isNumberedPag($string)
	{
		return in_array($string,$this->numberedPaginationStyles);
	}

	/**
	 * @param string
	 * @return string
	 */
	public function renderCustomCss($customCss)
	{
		$css = '<style type="text/css">';
		$css .= $customCss;
		$css .= '</style>';
		return $css;
	}

	public function getAvailableDesigns()
	{
		// Get the themes
		$themeList = Mage::getModel('core/design_package')->getThemeList();
		$availableDesigns = array();

		// Loop through the themes
		foreach($themeList as $package => $themes) {
			foreach($themes as $theme) {
				$availableDesigns[] = array(
					'label' => $package.'/'.$theme,
					'value' => $package.'/'.$theme
				);
			}
		}

		return $availableDesigns;
	}

	public function getAllowedThemes()
	{
		$configThemes = explode(',',Mage::getStoreConfig('homepage/options/allowed_themes'));
		$allowedThemes = array();
		foreach ($configThemes as $configTheme)
		{
			$allowedThemes[] = array('label' => $configTheme, 'value' => $configTheme);
		}
		return $allowedThemes;
	}
	
	public function isRemoveBaseUrl()
	{
		return Mage::getStoreConfigFlag('homepage/options/remove_base_url');
	}

}