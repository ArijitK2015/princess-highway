<?php

/**
 * Class FactoryX_Contests_Model_Mysql4_Referee
 */
class FactoryX_Contests_Model_Mysql4_Referee extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('contests/referee', 'referee_id');
    }
	
	/**
     * Load referee from DB by email and contest id
     *
     * @param string $refereeEmail
	 * @param int $contestId
     * @return array
     */
    public function loadByEmailAndContest($refereeEmail,$contestId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('contests/referee'))
            ->where('email=?',$refereeEmail)
			->where('contest_id=?',$contestId);

        $result = $this->_getReadAdapter()->fetchRow($select);

        if(!$result) {
            return array();
        }

        return $result;
    }
}