<?php

/**
 * Class FactoryX_MenuImage_Block_Adminhtml_Menuimage_Edit_Tabs
 */
class FactoryX_MenuImage_Block_Adminhtml_Menuimage_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	/**
	 *	Constructor for the tabs of the edit menuimage page
	 */
    public function __construct()
    {
        parent::__construct();
        $this->setId('menuimage_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('menuimage')->__('Menu Image Information'));
    }
 
	/**
	 * Prepare the HTML before displaying it
	 */
    protected function _beforeToHtml()
    {
		// We get the active tab and empty the session 
		$active = Mage::getSingleton('admin/session')->getActiveTab(true);

        // We retrieve the data from the session or the registered data
        if (Mage::getSingleton('adminhtml/session')->getMenuimageData())
        {
            $data = Mage::getSingleton('adminhtml/session')->getMenuimageData();
            Mage::getSingleton('adminhtml/session')->SetMenuimageData(null);
        }
        elseif (Mage::registry('menuimage_data'))
        {
            $data = Mage::registry('menuimage_data')->getData();
        }
        else $data = array();
		
		// Add the general tab
		$this->addTab('general_tab', array(
            'label'     => Mage::helper('menuimage')->__('General Information'),
            'title'     => Mage::helper('menuimage')->__('General Information'),
            'content'   => $this->getLayout()->createBlock('menuimage/adminhtml_menuimage_edit_tab_general')->toHtml(),
			'active'	=> $active == 'general_tab' ? true : false
        ));
		
        // Check is single store mode
        if (!Mage::app()->isSingleStoreMode()) 
		{
			// Add the store tab
			$this->addTab('store_tab', array(
				'label'     => Mage::helper('menuimage')->__('Store'),
				'title'     => Mage::helper('menuimage')->__('Store'),
				'content'   => $this->getLayout()->createBlock('menuimage/adminhtml_menuimage_edit_tab_store')->toHtml(),
				'active'	=> $active == 'store_tab' ? true : false
			));
        }
		
        return parent::_beforeToHtml();
    }
	
}