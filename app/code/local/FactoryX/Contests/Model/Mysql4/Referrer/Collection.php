<?php

class FactoryX_Contests_Model_Mysql4_Referrer_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('contests/referrer');
    }
	
	public function addContestFilter($contestId) {
        $this->getSelect()
                ->where('main_table.contest_id = ?', $contestId);
        return $this;
    }
	
	public function addStateFilter($state) {
        $this->getSelect()
                ->where('main_table.state = ?', $state);
        return $this;
    }
	
	public function addWinnersFilter() {
        $this->getSelect()
                ->where('main_table.is_winner = 1');
        return $this;
    }
	
	public function addContestData() {
        $this->getSelect()
                ->joinInner(
					array('contest' => $this->getTable('contests/contest')),
					'contest.contest_id = main_table.contest_id',
					array('contest_title'	=>	'title'));
        return $this;
    }
	
	public function addRefereeData() {
        $this->getSelect()
                ->joinInner(
					array('referee' => $this->getTable('contests/referee')),
					'referee.referrer_id = main_table.referrer_id',
					'*');
        return $this;
    }

}