<?php

class FactoryX_Contests_Model_Referrer extends Mage_Core_Model_Abstract 
{
	
    protected function _construct()
    {
        $this->_init('contests/referrer', 'referrer_id');
    }   
	
	/**
     * Load referrer data from resource model by email and contest id
     *
     * @param string $referrerEmail
	 * @param int $contestId
     */
	public function loadByEmailAndContest($referrerEmail, $contestId)
    {
        $this->addData($this->getResource()->loadByEmailAndContest($referrerEmail, $contestId));
        return $this;
    }
	
	/**
	 *
	 */
	public function wins()
	{
		$this->setIsWinner(1)->save();
	}
	
	/**
	 *
	 */
	public function reset()
	{
		$this->setIsWinner(0)->save();
	}
}