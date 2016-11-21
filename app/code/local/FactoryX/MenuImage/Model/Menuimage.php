<?php

/**
 * Class FactoryX_MenuImage_Model_Menuimage
 */
class FactoryX_MenuImage_Model_Menuimage extends Mage_Core_Model_Abstract
{
	/**
	 *	Constructor for the menuimage model
	 */
    protected function _construct()
    {
        $this->_init('menuimage/menuimage', 'menuimage_id');
    }
	
	/**
	 *	Check if viewable in store
	 */
	public function isStoreViewable()
	{
		$menuimages = Mage::getResourceModel('menuimage/menuimage_collection')
							->addIdFilter($this->getMenuimageId())
							->addStoreFilter();
		
		if (count($menuimages) == 1)
			return true;
		else 
			return false;
	}

    /**
     *    Getter for an menuimage block based on its index
     * @param $index
     * @return bool
     */
	public function getBlock($index)
	{
		$menuimageBlocks = Mage::getModel('menuimage/block')
							->getCollection()
							->addMenuimageFilter($this->getMenuimageId())
							->addIndexFilter($index);
							
		if (count($menuimageBlocks)==1) return $menuimageBlocks->getFirstItem();
		else return false;
	}

    /**
     *    Getter for several menuimage blocks based on an array of indexes
     * @param $indexes
     * @return bool
     */
	public function getBlocks($indexes)
	{
        $menuimageBlocks = Mage::getModel('menuimage/block')
							->getCollection()
							->addMenuimageFilter($this->getMenuimageId())
							->addIndexFilter($indexes);
							
		if (count($menuimageBlocks)) return $menuimageBlocks;
		else return false;
	}
	
	/**
	 *	Getter for all blocks of a menuimage
	 */
	public function getAllBlocks()
	{
        $menuimageBlocks = Mage::getModel('menuimage/block')
							->getCollection()
							->addMenuimageFilter($this->getMenuimageId());
							
		return $menuimageBlocks;
	}
}