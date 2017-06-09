<?php

/**
 * Class FactoryX_Homepage_Model_Mysql4_Image_Collection
 */
class FactoryX_Homepage_Model_Mysql4_Image_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

	/**
	 *	Constructor for the image collection model
	 */
    public function _construct() {
        parent::_construct();
        $this->_init('homepage/image');
    }

    /**
     *    Homepage filter
     * @param $id
     * @return $this
     */
	public function addHomepageFilter($id) {
        $this->getSelect()
                ->where('main_table.homepage_id = ?', $id);
        return $this;
    }

    /**
     *    Index filter
     * @param $index
     * @return $this
     */
	public function addIndexFilter($index) {
		if (!is_array($index))
		{
			$this->getSelect()
                ->where('main_table.index = ?', $index);
		}
		else
		{
			$this->getSelect()
					->where('main_table.index IN ('.implode(',',$index).')');
		}
        return $this;
    }

    /**
     *   Not over filter
     * @return $this
     */
    public function addNotOverFilter() {
        $this->getSelect()
            ->where('main_table.over = 0');
        return $this;
    }

    /**
     *    Over filter
     * @return $this
     */
    public function addOverFilter() {
        $this->getSelect()
            ->where('main_table.over = 1');
        return $this;
    }
}