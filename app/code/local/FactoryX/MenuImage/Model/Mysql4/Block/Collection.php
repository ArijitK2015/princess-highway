<?php

/**
 * Class FactoryX_MenuImage_Model_Mysql4_Block_Collection
 */
class FactoryX_MenuImage_Model_Mysql4_Block_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

	/**
	 *	Constructor for the block collection model
	 */
    public function _construct() {
        parent::_construct();
        $this->_init('menuimage/block');
    }

    /**
     *    Menuimage filter
     * @param $id
     * @return $this
     */
	public function addMenuimageFilter($id) {
        $this->getSelect()
                ->where('main_table.menuimage_id = ?', $id);
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