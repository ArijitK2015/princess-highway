<?php

/**
 * Class FactoryX_Contests_Model_Contest
 */
class FactoryX_Contests_Model_Contest extends Mage_Core_Model_Abstract
{

	const CACHE_TAG	= 'FX_CONTESTS_CACHE_TAG';
	
	public $arrayWinners = array();
	
    protected function _construct()
    {
        $this->_init('contests/contest', 'contest_id');
    }

	/**
	 * @return mixed
     */
	public function getUrlInStore()
	{			
		if (!Mage::app()->isSingleStoreMode())
		{
			// Database resources
			$resource = Mage::getSingleton('core/resource');
			$readConnection = $resource->getConnection('core_read');
			
			// Get the contest related stores
			$query = "SELECT store_id 
						FROM {$resource->getTableName('contests/store')}
						WHERE contest_id = {$this->getContestId()}";
			
			// We use the first store URL even if there's several stores for the same contest
			$contestStoreId = $readConnection->fetchOne($query);
								
			if ($contestStoreId)	return Mage::getUrl($this->getIdentifier(),array('_store'=>$contestStoreId));
		}
		else
		{
			return Mage::getUrl($this->getIdentifier());
		}
	}
	
	/**
	 *
	 */
	public function winnersCount()
	{			
		return count($this->getWinners());
	}
	
	/**
	 *
	 */
	public function getWinners()
	{
		$winners = Mage::getResourceModel('contests/referrer_collection')
							->addContestFilter($this->getContestId())
							->addWinnersFilter();
							
		return $winners;
	}
	
	/**
	 *
	 */
	public function isStoreViewable()
	{
		$contests = Mage::getResourceModel('contests/contest_collection')
							->addIdFilter($this->getContestId())
							->addStoreFilter();
		
		if (count($contests) == 1) 
			return true;
		else 
			return false;
	}
	
	/**
	 *
	 */
	public function isAllowedDuplicateEntries()
	{
		return $this->getAllowDuplicateEntries();
	}
	
	/**
	 *
	 */
	public function isAllowedDuplicateReferrals()
	{
		return $this->getAllowDuplicateReferrals();
	}
	
	/**
	 *
	 */
	public function isAutomatic()
	{
		return ($this->getStatus()==2) ? 1 : 0;
	}
	
	/**
	 *
	 */
	public function isGiveAwayContest()
	{
		return ($this->getType()==2) ? 1 : 0;
	}
	
	/**
	 *
	 */
	public function isReferAFriendContest()
	{
		return ($this->getType()==1) ? 1 : 0;
	}

	/**
	 * @param $numbersToDraw
	 * @param null $states
	 * @return int
	 */
	public function drawWinners($numbersToDraw,$states = null)
	{

		try
		{
			// If winners have already been drawn, we reset the winners
			if ($this->winnersCount() > 0)
			{
				$oldWinners = $this->getWinners();
				foreach ($oldWinners as $oldWinner)
				{
					Mage::getModel('contests/referrer')->load($oldWinner['referrer_id'])->reset();
				}
			}
			
			if ($this->isReferAFriendContest())
			{
				$arrayWinners = $this->getResource()->drawRafWinners($this,$numbersToDraw,$states);
			}
			elseif($this->isGiveAwayContest())
			{
				$arrayWinners = $this->getResource()->drawGaWinners($this,$numbersToDraw,$states);
			}
			
			foreach ($arrayWinners as $winnerId)
			{
				Mage::getModel('contests/referrer')->load($winnerId)->wins();
			}
			
			// Set the winners tab as the active tab
			Mage::getSingleton('admin/session')->setActiveTab('winners_tab');	

			// Return number of winners
			return count($arrayWinners);
		}
		catch (Exception $e)
		{
			Mage::getSingleton('admin/session')->setActiveTab('general_tab');
			Mage::helper('contests')->log($e->getMessage());
		}
		
	}
}