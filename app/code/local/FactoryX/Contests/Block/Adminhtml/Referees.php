<?php

class FactoryX_Contests_Block_Adminhtml_Referees extends Mage_Adminhtml_Block_Widget_Grid_Container 
{
	/**
	 * Constructor
	 */
    public function __construct() 
	{
        $this->_controller = 'adminhtml_referees';
        $this->_blockGroup = 'contests';
        $this->_headerText = Mage::helper('contests')->__('Referees List');
        parent::__construct();
        $this->setTemplate('factoryx/contests/list.phtml');
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
		// Display the grid
        $this->setChild('grid', $this->getLayout()->createBlock('contests/adminhtml_referees_grid', 'referees.grid'));
        return parent::_prepareLayout();
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