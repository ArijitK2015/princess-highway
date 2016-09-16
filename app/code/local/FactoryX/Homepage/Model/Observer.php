<?php

/**
 * Class FactoryX_Homepage_Model_Observer
 */
class FactoryX_Homepage_Model_Observer extends Mage_Core_Model_Abstract
{

	public function toggleHomepages($dryrun = false)
	{
		$this->disableHomepages($dryrun);
		$this->enableHomepages($dryrun);
	}

	/**
	 * Automatically disable the homepages when the end date is reached
	 * @param boolean $dryrun if set to true, it won't disable the homepage
	 */
	public function disableHomepages($dryrun = false) 
	{
		try
		{
	
			// Current date			
			$today = Mage::app()->getLocale()->date();
			
			// Get all automatic homepages
			$homepages = Mage::getResourceModel('homepage/homepage_collection')->addStatusFilter(FactoryX_Homepage_Model_Status::STATUS_AUTOMATIC);
			
			// Foreach homepage compare end date with today's date
			foreach ($homepages as $homepage)
			{
				$endDate = Mage::app()->getLocale()->date($homepage->getEndDate(), Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),null, true);
				$endDate->set('00:00:00',Zend_Date::TIMES);
				
				if ($today->isLater($endDate))
				{
					if ($homepage->getDisplayed())
					{
						// Hide and save
						$homepage->setDisplayed(0);
						if (!$dryrun)
						{
                            $homepage->getResource()->saveAttribute($homepage,array('displayed'));
						}
					}
				}
			}
			// Clean cache of the homepages to avoid disabled homepages to be displayed
			Mage::app()->cleanCache(FactoryX_Homepage_Model_Homepage::CACHE_TAG);
		}
		catch (Exception $e)
		{
			Mage::helper('homepage')->log("Exception caught in %s under % with message: %s", __FILE__, __FUNCTION__, $e->getMessage());
		}
		
	}
	
	/**
	 * Automatically enable the homepages when the start date is reached
	 * @param boolean $dryrun if set to true, it won't enable the homepage
	 */
	public function enableHomepages($dryrun = false) 
	{
		try
		{
	
			// Current date			
			$today = Mage::app()->getLocale()->date();
			
			// Get all automatic homepages
			$homepages = Mage::getResourceModel('homepage/homepage_collection')->addStatusFilter(FactoryX_Homepage_Model_Status::STATUS_AUTOMATIC);
			
			// Foreach homepage compare start date with today's date
			foreach ($homepages as $homepage)
			{
				$startDate = Mage::app()->getLocale()->date($homepage->getStartDate(), Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),null, true);
				$endDate = Mage::app()->getLocale()->date($homepage->getEndDate(), Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),null, true);
				$startDate->set('00:00:00',Zend_Date::TIMES);
				$endDate->set('00:00:00',Zend_Date::TIMES);
				
				if ($startDate->isEarlier($today) && $endDate->isLater($today))
				{
					if (!$homepage->getDisplayed())
					{
						// Display and save
						$homepage->setDisplayed(1);
						if (!$dryrun)
						{
                            $homepage->getResource()->saveAttribute($homepage,array('displayed'));
						}
					}
				}
			}
			// Clean cache of the homepages to avoid disabled homepages to be displayed
			Mage::app()->cleanCache(FactoryX_Homepage_Model_Homepage::CACHE_TAG);
		}
		catch (Exception $e)
		{
			Mage::helper('homepage')->log("Exception caught in %s under % with message: %s", __FILE__, __FUNCTION__, $e->getMessage());
		}
		
	}

	public function setHandle(Varien_Event_Observer $observer)
	{
		$fullActionName = $observer->getEvent()->getAction()->getFullActionName();
		if ($fullActionName == "cms_index_index")
		{
			$position = Mage::helper('homepage')->getPosition();
			if ($position=="before")
			{
				Mage::app()->getLayout()->getUpdate()->addHandle('factoryx_homepage_before');
			}
			else
			{
				Mage::app()->getLayout()->getUpdate()->addHandle('factoryx_homepage_after');
			}
		}
	}
}