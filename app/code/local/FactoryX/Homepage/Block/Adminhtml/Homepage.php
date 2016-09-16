<?php

/**
 * Class FactoryX_Homepage_Block_Adminhtml_Homepage
 */
class FactoryX_Homepage_Block_Adminhtml_Homepage extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	/**
	 * Constructor
	 */
    public function __construct() 
	{
        $this->_controller = 'adminhtml_homepage';
        $this->_blockGroup = 'homepage';
        $this->_headerText = Mage::helper('homepage')->__('Home Pages Manager');
        parent::__construct();
        $this->setTemplate('factoryx/homepage/list.phtml');
    }

	/**
	 * Prepare the layout
	 */
    protected function _prepareLayout() 
	{
		// Add new homepage buton
        $this->setChild('add_new_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label' => Mage::helper('homepage')->__('Add Home Page'),
                            'onclick' => "setLocation('" . $this->getUrl('*/*/details') . "')",
                            'class' => 'add'
                        ))
        );
		
		// Preview homepage buton
        $this->setChild('preview_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label' => Mage::helper('homepage')->__('Preview Store Home Page'),
                            'onclick' => "previewStoreHomepage()",
                            'class' => 'save'
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
        $this->setChild('grid', $this->getLayout()->createBlock('homepage/adminhtml_homepage_grid', 'homepage.grid'));
        return parent::_prepareLayout();
    }

	/**
	 * Getter for the add new homepage button
	 */
    public function getAddNewButtonHtml() 
	{
        return $this->getChildHtml('add_new_button');
    }
	
	/**
	 * Getter for the preview homepage button
	 */
    public function getPreviewButtonHtml() 
	{
        return $this->getChildHtml('preview_button');
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