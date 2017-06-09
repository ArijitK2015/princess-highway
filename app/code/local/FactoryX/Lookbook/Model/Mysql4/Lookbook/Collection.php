<?php

/**
 * Class FactoryX_Lookbook_Model_Mysql4_Lookbook_Collection
 */
class FactoryX_Lookbook_Model_Mysql4_Lookbook_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    /**
     *	Constructor for lookbook collection model
     */
    public function _construct() {
        parent::_construct();
        $this->_init('lookbook/lookbook');
    }

    /**
     *    Inclusion filter
     * @param $ids
     * @return $this
     */
    public function addIdsFilter($ids) {
        if (!is_array($ids))
        {
            $this->getSelect()
                ->where('main_table.lookbook_id = ?', $ids);
        }
        else
        {
            $ids = join(',',$ids);
            $this->getSelect()
                ->where('main_table.lookbook_id IN ('.implode(',',$ids).')');
        }
        return $this;
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
                ->where('main_table.lookbook_id <> ?', $ids);
        }
        else
        {
            $ids = join(',',$ids);
            $this->getSelect()
                ->where('main_table.lookbook_id NOT IN ('.implode(',',$ids).')');
        }
        return $this;
    }

    /**
     *    Include In Nav filter
     * @param $include
     * @return $this
     */
    public function addIncludeInNavFilter($include) {
        $this->getSelect()
            ->where('main_table.include_in_nav = ?', $include);
        return $this;
    }

    /**
     *    Nav Category filter
     * @param $category
     * @return $this
     * @internal param $include
     */
    public function addNavCategoryFilter($category) {
        $this->getSelect()
            ->where('main_table.nav_category = ?', $category);
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
                array('store_table' => $this->getTable('lookbook/store')), 'main_table.lookbook_id = store_table.lookbook_id', array()
            )
                ->where('store_table.store_id in (?)', array(0, $store));

            return $this;
        }
        return $this;
    }

    /**
     * @param $attribute
     * @param string $order
     * @return $this
     */
    public function addAttributeToSort($attribute, $order = 'asc')
    {
        $this->getSelect()
            ->order('main_table.'.$attribute.' '.$order);
        return $this;
    }

}