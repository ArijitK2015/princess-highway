<?php

class FactoryX_Contests_Model_Observer extends Mage_Core_Model_Abstract 
{

	/**
	 * Automatically disable the contests when the end date is reached
	 * @param boolean if dryrun is set to true, it won't disable the contest
	 */
	public function disableContests($dryrun = false) 
	{
		try
		{
	
			// Current date			
			$today = Mage::app()->getLocale()->date();
			
			// Get all automatic contests
			$contests = Mage::getResourceModel('contests/contest_collection')->addStatusFilter(2);
			
			// Foreach contest compare end date with today's date
			foreach ($contests as $contest)
			{
				$endDate = Mage::app()->getLocale()->date($contest->getEndDate(), Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),null, true);
				$endDate->set('00:00:00',Zend_Date::TIMES);
				
				if ($today->isLater($endDate))
				{
					if ($contest->getDisplayed())
					{
						// Hide and save
						$contest->setDisplayed(0);
						if (!$dryrun)
						{
							$contest->save();
						}
					}
				}
			}
			// Clean cache of the contests to avoid disabled contests to be displayed
			Mage::app()->cleanCache(FactoryX_Contests_Model_Contest::CACHE_TAG);
		}
		catch (Exception $e)
		{
			Mage::helper('contests')->log("Exception caught in %s under % with message: %s", __FILE__, __FUNCTION__, $e->getMessage());
		}
		
	}
	
	/**
	 * Automatically enable the contests when the start date is reached
	 * @param boolean if dryrun is set to true, it won't enable the contest
	 */
	public function enableContests($dryrun = false) 
	{
		try
		{
	
			// Current date			
			$today = Mage::app()->getLocale()->date();
			
			// Get all automatic contests
			$contests = Mage::getResourceModel('contests/contest_collection')->addStatusFilter(2);
			
			// Foreach contest compare start date with today's date
			foreach ($contests as $contest)
			{
				$startDate = Mage::app()->getLocale()->date($contest->getStartDate(), Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),null, true);
				$startDate->set('00:00:00',Zend_Date::TIMES);
				
				if ($today->isLater($startDate))
				{
					if (!$contest->getDisplayed())
					{
						// Display and save
						$contest->setDisplayed(1);
						if (!$dryrun)
						{
							$contest->save();
						}
					}
				}
			}
			// Clean cache of the contests to avoid disabled contests to be displayed
			Mage::app()->cleanCache(FactoryX_Contests_Model_Contest::CACHE_TAG);
		}
		catch (Exception $e)
		{
			Mage::helper('contests')->log("Exception caught in %s under % with message: %s", __FILE__, __FUNCTION__, $e->getMessage());
		}
		
	}
}