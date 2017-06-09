<?php

/**
 * Class FactoryX_Lookbook_Block_Adminhtml_Lookbook
 */
class FactoryX_Lookbook_Block_Adminhtml_Lookbook extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	/**
	 * Constructor
	 */
    public function __construct() 
	{
        $this->_controller = 'adminhtml_lookbook';
        $this->_blockGroup = 'lookbook';
        $this->_headerText = Mage::helper('lookbook')->__('Lookbooks Manager');
        parent::__construct();
        $this->setTemplate('factoryx/lookbook/list.phtml');
    }

	/**
	 * Prepare the layout
	 */
    protected function _prepareLayout() 
	{
		// Add new lookbook buton
        $this->setChild('add_new_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label' => Mage::helper('lookbook')->__('Add Lookbook'),
                            'onclick' => "setLocation('" . $this->getUrl('*/*/choosecat') . "')",
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
        $this->setChild('grid', $this->getLayout()->createBlock('lookbook/adminhtml_lookbook_grid', 'lookbook.grid'));
        return parent::_prepareLayout();
    }

	/**
	 * Getter for the add new lookbook button
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