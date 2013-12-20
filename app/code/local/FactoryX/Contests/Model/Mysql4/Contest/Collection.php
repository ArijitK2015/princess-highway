<?php

class FactoryX_Contests_Model_Mysql4_Contest_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('contests/contest');
    }
	
	public function addIsPopupFilter($isPopup) {
        $this->getSelect()
                ->where('main_table.is_popup = ?', $isPopup);
        return $this;
    }
	
	public function addNotIdsFilter($ids) {
		if (!is_array($ids))
		{
			$this->getSelect()
					->where('main_table.contest_id <> ?', $ids);
		}
		else
		{
			$this->getSelect()
					->where('main_table.contest_id NOT IN ?', $ids);
		}
        return $this;
    }
	
	public function addIdFilter($id) {
        $this->getSelect()
                ->where('main_table.contest_id = ?', $id);
        return $this;
    }
	
	public function addInListFilter($inList) {
        $this->getSelect()
                ->where('main_table.is_in_list = ?', $inList);
        return $this;
    }
	
	public function addDisplayedFilter($displayed) {
        $this->getSelect()
                ->where('main_table.displayed = ?', $displayed);
        return $this;
    }

    public function addStatusFilter($status) {
        $this->getSelect()
                ->where('main_table.status = ?', $status);
        return $this;
    }

    public function addPresentFilter() {
        $this->getSelect()
                ->where('main_table.added <= ?', now());
        return $this;
    }
    
    /**
     * Add Filter by store
     *
     * @param int|Mage_Core_Model_Store $store
     * @return Mage_Cms_Model_Mysql4_Page_Collection
     */
    public function addStoreFilter($store = null) {
        if ($store === null)
            $store = Mage::app()->getStore()->getId();
        if (!Mage::app()->isSingleStoreMode()) {
            if ($store instanceof Mage_Core_Model_Store) {
                $store = array($store->getId());
            }

            $this->getSelect()->joinLeft(
                            array('store_table' => $this->getTable('contests/store')), 'main_table.contest_id = store_table.contest_id', array()
                    )
                    ->where('store_table.store_id in (?)', array(0, $store));

            return $this;
        }
        return $this;
    }

}