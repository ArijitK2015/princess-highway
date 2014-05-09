<?php

class FactoryX_Homepage_Model_Mysql4_Homepage_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

	/**
	 *	Constructor for homepage collection model
	 */
    public function _construct() {
        parent::_construct();
        $this->_init('homepage/homepage');
    }
	
	/**
	 *	Exclusion filter
	 */
	public function addNotIdsFilter($ids) {
		if (!is_array($ids))
		{
			$this->getSelect()
					->where('main_table.homepage_id <> ?', $ids);
		}
		else
		{
			$ids = join(',',$ids);
			$this->getSelect()
					->where('main_table.homepage_id NOT IN ('.implode(',',$ids).')');
		}
        return $this;
    }
	
	/**
	 *	Inclusion filter
	 */
	public function addIdFilter($id) {
        $this->getSelect()
                ->where('main_table.homepage_id = ?', $id);
        return $this;
    }
	
	/**
	 *	Displayed filter
	 */
    public function addDisplayedFilter($displayed) {
        $this->getSelect()
                ->where('main_table.displayed = ?', $displayed);
        return $this;
    }

	/**
	 *	Status filter
	 */
    public function addStatusFilter($status) {
        $this->getSelect()
                ->where('main_table.status = ?', $status);
        return $this;
    }

	/**
	 *	Date filter
	 */
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
                            array('store_table' => $this->getTable('homepage/store')), 'main_table.homepage_id = store_table.homepage_id', array()
                    )
                    ->where('store_table.store_id in (?)', array(0, $store));

            return $this;
        }
        return $this;
    }
	
	public function addAttributeToSort($attribute, $order = 'asc')
	{
		$this->getSelect()
                ->order('main_table.'.$attribute.' '.$order);
        return $this;
	}

}