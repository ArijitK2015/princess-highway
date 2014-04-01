<?php

class FactoryX_Homepage_Model_Mysql4_Image_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

	/**
	 *	Constructor for the image collection model
	 */
    public function _construct() {
        parent::_construct();
        $this->_init('homepage/image');
    }
	
	/**
	 *	Homepage filter
	 */
	public function addHomepageFilter($id) {
        $this->getSelect()
                ->where('main_table.homepage_id = ?', $id);
        return $this;
    }
	
	/**
	 *	Index filter
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
}