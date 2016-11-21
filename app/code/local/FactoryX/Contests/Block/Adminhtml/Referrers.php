<?php

/**
 * Class FactoryX_Contests_Block_Adminhtml_Referrers
 */
class FactoryX_Contests_Block_Adminhtml_Referrers extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	/**
	 * Constructor
	 */
    public function __construct() 
	{
        $this->_controller = 'adminhtml_referrers';
        $this->_blockGroup = 'contests';
        $this->_headerText = Mage::helper('contests')->__('Referrers List');
        $this->_removeButton('add');
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