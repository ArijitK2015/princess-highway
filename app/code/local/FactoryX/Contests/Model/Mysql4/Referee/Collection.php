<?php

/**
 * Class FactoryX_Contests_Model_Mysql4_Referee_Collection
 */
class FactoryX_Contests_Model_Mysql4_Referee_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('contests/referee');
    }

    /**
     * @param $contestId
     * @return $this
     */
    public function addContestFilter($contestId) {
        $this->getSelect()
                ->where('main_table.contest_id = ?', $contestId);
        return $this;
    }

    /**
     * @return $this
     */
    public function addContestData() {
        $this->getSelect()
                ->joinInner(
					array('contest' => $this->getTable('contests/contest')),
					'contest.contest_id = main_table.contest_id',
					array('contest_title'	=>	'title'));
        return $this;
    }

    /**
     * @return $this
     */
    public function addReferrerData() {
        $this->getSelect()
                ->joinInner(
					array('referrer' => $this->getTable('contests/referrer')),
					'referrer.referrer_id = main_table.referrer_id',
					array('referrer_email'	=>	'email'));
        return $this;
    }

    /**
     * @param $email
     * @return $this
     */
    public function filterByReferrerEmail($email) {
		$this->addReferrerData();
        $this->getSelect()
                ->where('referrer.email = ?', $email);
        return $this;
    }

}