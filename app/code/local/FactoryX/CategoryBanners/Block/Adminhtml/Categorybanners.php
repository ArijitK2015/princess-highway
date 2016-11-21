<?php

/**
 * Class FactoryX_CategoryBanners_Block_Adminhtml_Categorybanners
 * This is the parent block of the grid
 */
class FactoryX_CategoryBanners_Block_Adminhtml_Categorybanners extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_categorybanners';
        $this->_blockGroup = 'categorybanners';
        $this->_headerText = Mage::helper('categorybanners')->__('Category Banners Manager');
        $this->_addButtonLabel = Mage::helper('categorybanners')->__('Add Category Banner');
        parent::__construct();
    }

}