<?php

/**
 * Class FactoryX_MenuImage_Model_Mysql4_Menuimage_Collection
 */
class FactoryX_MenuImage_Model_Mysql4_Menuimage_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

	/**
	 *	Constructor for homepage collection model
	 */
    public function _construct() {
        parent::_construct();
        $this->_init('menuimage/menuimage');
    }

    /**
     * Exclusion filter
     * @param $ids
     * @return $this
     */
	public function addNotIdsFilter($ids) {
		if (!is_array($ids))
		{
			$this->getSelect()
					->where('main_table.menuimage_id <> ?', $ids);
		}
		else
		{
			$ids = join(',',$ids);
			$this->getSelect()
					->where('main_table.menuimage_id NOT IN ('.implode(',',$ids).')');
		}
        return $this;
    }

    /**
     * Inclusion filter
     * @param $id
     * @return $this
     */
	public function addIdFilter($id) {
        $this->getSelect()
                ->where('main_table.menuimage_id = ?', $id);
        return $this;
    }

    /**
     * Category filter
     * @param $id
     * @return $this
     */
	public function addCategoryFilter($id) {
        if (strpos("category/",$id) === false) {
            $id = "category/" . $id;
        }
        $this->getSelect()
                ->where('main_table.category_id = ?', $id);
        return $this;
    }

    /**
     * Status filter
     * @param $status
     * @return $this
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
                            array('store_table' => $this->getTable('menuimage/store')), 'main_table.menuimage_id = store_table.menuimage_id', array()
                    )
                    ->where('store_table.store_id in (?)', array(0, $store));

            return $this;
        }
        return $this;
    }

}