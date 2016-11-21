<?php

/**
 * Class FactoryX_CategoryBanners_Model_Mysql4_Banner_Collection
 */
class FactoryX_CategoryBanners_Model_Mysql4_Banner_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

	/**
	 *	Constructor for banner collection model
	 */
    public function _construct() {
        parent::_construct();
        $this->_init('categorybanners/banner');
    }

    /**
     *    Exclusion filter
     * @param $ids
     * @return $this
     */
	public function addNotIdsFilter($ids) {
		if (!is_array($ids))
		{
			$this->getSelect()
					->where('main_table.banner_id <> ?', $ids);
		}
		else
		{
			$ids = join(',',$ids);
			$this->getSelect()
					->where('main_table.banner_id NOT IN ('.implode(',',$ids).')');
		}
        return $this;
    }

    /**
     *    Inclusion filter
     * @param $id
     * @return $this
     */
	public function addIdFilter($id) {
        $this->getSelect()
                ->where('main_table.banner_id = ?', $id);
        return $this;
    }

    /**
     * Category filter
     * @param $id
     * @return $this
     */
    public function addCatFilter($id) {
        $this->getSelect()
            ->where('main_table.category_id = ?', $id);
        return $this;
    }

    /**
     *    Displayed filter
     * @param $displayed
     * @return $this
     */
    public function addDisplayedFilter($displayed) {
        $this->getSelect()
                ->where('main_table.displayed = ?', $displayed);
        return $this;
    }

    /**
     * Display on children filter
     * @param $flag
     * @return $this
     */
    public function addDisplayOnChildren($flag) {
        $this->getSelect()
            ->where('main_table.display_on_children = ?', $flag);
        return $this;
    }

    /**
     *    Status filter
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
}