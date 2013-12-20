<?php

class FactoryX_Contests_Model_Mysql4_Referrer extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('contests/referrer', 'referrer_id');
    } 

	/**
     * Load referrer from DB by email and contest id
     *
     * @param string $referrerEmail
	 * @param int $contestId
     * @return array
     */
    public function loadByEmailAndContest($referrerEmail, $contestId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('contests/referrer'))
            ->where('email=?',$referrerEmail)
			->where('contest_id=?',$contestId);

        $result = $this->_getReadAdapter()->fetchRow($select);

        if(!$result) {
            return array();
        }

        return $result;
    }
}