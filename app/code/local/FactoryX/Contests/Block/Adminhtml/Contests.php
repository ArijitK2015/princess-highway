<?php

class FactoryX_Contests_Block_Adminhtml_Contests extends Mage_Adminhtml_Block_Widget_Grid_Container 
{
	/**
	 * Constructor
	 */
    public function __construct() 
	{
        $this->_controller = 'adminhtml_contests';
        $this->_blockGroup = 'contests';
        $this->_headerText = Mage::helper('contests')->__('Contests Manager');
        parent::__construct();
        $this->setTemplate('factoryx/contests/list.phtml');
    }

	/**
	 * Prepare the layout
	 */
    protected function _prepareLayout() 
	{
		// Add new contest buton
        $this->setChild('add_new_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label' => Mage::helper('contests')->__('Add Contest'),
                            'onclick' => "setLocation('" . $this->getUrl('*/*/new') . "')",
                            'class' => 'add'
                        ))
        );
		
        // Display store switcher if system has more one store
        if (!Mage::app()->isSingleStoreMode()) 
		{
            $this->setChild('store_switcher', $this->getLayout()->createBlock('adminhtml/store_switcher')
                            ->setUseConfirm(false)
                            ->setSwitchUrl($this->getUrl('*/*/*', array('store' => null)))
            );
        }
		// Add the grid
        $this->setChild('grid', $this->getLayout()->createBlock('contests/adminhtml_contests_grid', 'contests.grid'));
        return parent::_prepareLayout();
    }

	/**
	 * Getter for the add new contest button
	 */
    public function getAddNewButtonHtml() 
	{
        return $this->getChildHtml('add_new_button');
    }

	/**
	 * Getter for the grid HTML
	 */
    public function getGridHtml() 
	{
        return $this->getChildHtml('grid');
    }

	/**
	 * Getter for the store switcher HTML
	 */
    public function getStoreSwitcherHtml() 
	{
        return $this->getChildHtml('store_switcher');
    }

}