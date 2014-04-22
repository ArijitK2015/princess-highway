<?php
class FactoryX_Lookbook_Model_Mysql4_Lookbook_Media_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Construct method
     */
    public function _construct()
    {
        $this->_init('lookbook/lookbook_media');
    }
	
	public function addAttributeToSort($attribute, $order = 'asc')
	{
		$this->getSelect()
                ->order('main_table.'.$attribute.' '.$order);
        return $this;
	}

}