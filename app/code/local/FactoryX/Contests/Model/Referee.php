<?php

class FactoryX_Contests_Model_Referee extends Mage_Core_Model_Abstract 
{
	
    protected function _construct()
    {
        $this->_init('contests/referee', 'referee_id');
    }
	
	/**
     * Load referee data from resource model by email and contest id
     *
     * @param string $refereeEmail
	 * @param int $contestId
     */
	public function loadByEmailAndContest($refereeEmail, $contestId)
    {
        $this->addData($this->getResource()->loadByEmailAndContest($refereeEmail, $contestId));
        return $this;
    }
}