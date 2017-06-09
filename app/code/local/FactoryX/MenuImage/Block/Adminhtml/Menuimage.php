<?php

/**
 * Class FactoryX_MenuImage_Block_Adminhtml_Menuimage
 */
class FactoryX_MenuImage_Block_Adminhtml_Menuimage extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	/**
	 * Constructor
	 */
    public function __construct() 
	{
        $this->_controller = 'adminhtml_menuimage';
        $this->_blockGroup = 'menuimage';
        $this->_headerText = Mage::helper('menuimage')->__('Menu Images Manager');
        $this->_addButtonLabel = Mage::helper('menuimage')->__('Add Menu Image');
        parent::__construct();
        $this->setTemplate('factoryx/menuimage/list.phtml');
    }

	/**
	 * Prepare the layout
	 */
    protected function _prepareLayout() 
	{
        // Display store switcher if system has more one store
        if (!Mage::app()->isSingleStoreMode()) 
		{
            $this->setChild('store_switcher', $this->getLayout()->createBlock('adminhtml/store_switcher')
                            ->setUseConfirm(false)
                            ->setSwitchUrl($this->getUrl('*/*/*', array('store' => null)))
            );
        }
        return parent::_prepareLayout();
    }

	/**
	 * Getter for the store switcher HTML
	 */
    public function getStoreSwitcherHtml() 
	{
        return $this->getChildHtml('store_switcher');
    }

}