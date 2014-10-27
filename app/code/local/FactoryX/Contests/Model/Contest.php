<?php

class FactoryX_Contests_Model_Contest extends Mage_Core_Model_Abstract 
{

	const CACHE_TAG	= 'contests_contest';
	
	public $arrayWinners = array();
	
    protected function _construct()
    {
        $this->_init('contests/contest', 'contest_id');
    }
	
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
	
	public function generateWinnersArray($args)
	{
		$this->arrayWinners[] = $args['row']['referrer_id'];
	}
	
	/**
	 *
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
				// Get the winners using the old ReferAFriend module method with a temporary table
				$resource = Mage::getSingleton('core/resource');
				$writeConnection = $resource->getConnection('core_write');
				$readConnection = $resource->getConnection('core_read');
				
				if ($writeConnection->isTableExists('fx_contests_winner_tmp'))
				{
					$writeConnection->dropTable('fx_contests_winner_tmp');
					
				}
				
				$table = new Varien_Db_Ddl_Table();
				$table->setName($resource->getTableName('contests/contest_winner'));
				$table->addColumn('email', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255);
				$table->addColumn('referrer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11);
				$table->addColumn('state', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50);
				$table->setOption('type', 'InnoDB');
				$table->setOption('charset', 'utf8');
				$writeConnection->createTable($table);
				
				// 1 entry per referee
				$query = "INSERT INTO {$resource->getTableName('contests/contest_winner')}
							SELECT r1.email, r1.referrer_id, r1.state
							FROM {$resource->getTableName('contests/referrer')} r1 
							INNER JOIN {$resource->getTableName('contests/referee')} r2 
							ON r1.referrer_id = r2.referrer_id 
							AND r1.contest_id = {$this->getContestId()}";
				
				$writeConnection->query($query);
												
				// Plus 1 extra entry per referrer
				$query = "INSERT INTO {$resource->getTableName('contests/contest_winner')}
							SELECT DISTINCT r1.email, r1.referrer_id, r1.state
							FROM {$resource->getTableName('contests/referrer')} r1 
							WHERE contest_id = {$this->getContestId()}";
				
				$writeConnection->query($query);
				
				if ($states)
				{
					// Single state
					if (!is_array($states))
					{
						// Select the winner(s) randomly one by one (so we don't pick someone who has already win) per state
						for ($i = 0; $i < $numbersToDraw; $i++)
						{
							$query = "SELECT referrer_id 
									FROM {$resource->getTableName('contests/contest_winner')}
									WHERE state = '$states'
									ORDER BY RAND()
									LIMIT 1";
							
							$winnerReferrerId = $readConnection->fetchOne($query);
							
							$this->arrayWinners[] = $winnerReferrerId;
							
							// Remove every entry with the winner referrer id
							$query = "DELETE FROM {$resource->getTableName('contests/contest_winner')}
										WHERE referrer_id = {$winnerReferrerId}";
							
							$writeConnection->query($query);
						}
					}
					else
					{
						foreach($states as $state)
						{
							for ($i = 0; $i < $numbersToDraw; $i++)
							{
								$query = "SELECT referrer_id 
										FROM {$resource->getTableName('contests/contest_winner')}
										WHERE state = '$state'
										ORDER BY RAND()
										LIMIT 1";
								
								if ($winnerReferrerId = $readConnection->fetchOne($query))
								{
									$this->arrayWinners[] = $winnerReferrerId;
								
									// Remove every entry with the winner referrer id
									$query = "DELETE FROM {$resource->getTableName('contests/contest_winner')}
												WHERE referrer_id = {$winnerReferrerId}";
								
									$writeConnection->query($query);
								}
							}
						}
					}
				}
				else
				{
					// Select the winner(s) randomly one by one (so we don't pick someone who has already win)
					for ($i = 0; $i < $numbersToDraw; $i++)
					{
						$query = "SELECT referrer_id 
								FROM {$resource->getTableName('contests/contest_winner')}
								ORDER BY RAND()
								LIMIT 1";
						
						$winnerReferrerId = $readConnection->fetchOne($query);
						
						$this->arrayWinners[] = $winnerReferrerId;
						
						// Remove every entry with the winner referrer id
						$query = "DELETE FROM {$resource->getTableName('contests/contest_winner')}
									WHERE referrer_id = {$winnerReferrerId}";
						
						$writeConnection->query($query);
					}
				}
				
			}
			elseif($this->isGiveAwayContest())
			{							
				if ($states)
				{
					// Single state
					if (!is_array($states))
					{
						$collection = Mage::getResourceModel('contests/referrer_collection');
						$collection->addContestFilter($this->getContestId());
						$collection->addStateFilter($states);
						$collection->getSelect()->order(new Zend_Db_Expr('RAND()'));
						$collection->getSelect()->limit($numbersToDraw);
						
						// Call iterator walk method with collection query string and callback method as parameters
						// Has to be used to handle massive collection instead of foreach
						Mage::getSingleton('core/resource_iterator')->walk($collection->getSelect(), array(array($this, 'generateWinnersArray')));
					}
					else
					{
						foreach($states as $state)
						{
							for ($i = 0; $i < $numbersToDraw; $i++)
							{
								$collection = Mage::getResourceModel('contests/referrer_collection');
								$collection->addContestFilter($this->getContestId());
								$collection->addStateFilter($state);
								$collection->getSelect()->order(new Zend_Db_Expr('RAND()'));
								$collection->getSelect()->limit(1);
								$collection->load();
								if ($winnerReferrerId = $collection->getFirstItem()->getReferrerId())
								{
									$this->arrayWinners[] = $winnerReferrerId;
								}
							}
						}
					}
				}
				else
				{
					$collection = Mage::getResourceModel('contests/referrer_collection');
					$collection->addContestFilter($this->getContestId());
					$collection->getSelect()->order(new Zend_Db_Expr('RAND()'));
					$collection->getSelect()->limit($numbersToDraw);
					
					// Call iterator walk method with collection query string and callback method as parameters
					// Has to be used to handle massive collection instead of foreach
					Mage::getSingleton('core/resource_iterator')->walk($collection->getSelect(), array(array($this, 'generateWinnersArray')));
				}
			}
			
			foreach ($this->arrayWinners as $winnerId)
			{
				Mage::getModel('contests/referrer')->load($winnerId)->wins();
			}
			
			// Set the winners tab as the active tab
			Mage::getSingleton('admin/session')->setActiveTab('winners_tab');	

			// Return number of winners
			return count($this->arrayWinners);
		}
		catch (Exception $e)
		{
			Mage::getSingleton('admin/session')->setActiveTab('general_tab');
			Mage::helper('contests')->log($e->getMessage());
		}
		
	}
}