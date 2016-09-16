<?php

/**
 * Class FactoryX_Homepage_Block_Adminhtml_Homepage_Edit_Tabs
 */
class FactoryX_Homepage_Block_Adminhtml_Homepage_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	/**
	 *	Constructor for the tabs of the edit homepage page
	 */
    public function __construct()
    {
        parent::__construct();
        $this->setId('homepage_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('homepage')->__('Home Page Information'));
    }
 
	/**
	 * Prepare the HTML before displaying it
	 */
    protected function _beforeToHtml()
    {
		// We get the active tab and empty the session 
		$active = Mage::getSingleton('admin/session')->getActiveTab(true);

        // We retrieve the data from the session or the registered data
        if (Mage::getSingleton('adminhtml/session')->getHomepageData())
        {
            $data = Mage::getSingleton('adminhtml/session')->getHomepageData();
            Mage::getSingleton('adminhtml/session')->SetHomepageData(null);
        }
        elseif (Mage::registry('homepage_data'))
        {
            $data = Mage::registry('homepage_data')->getData();
        }
        else $data = array();
		
		// Add the general tab
		$this->addTab('general_tab', array(
            'label'     => Mage::helper('homepage')->__('General Information'),
            'title'     => Mage::helper('homepage')->__('General Information'),
            'content'   => $this->getLayout()->createBlock('homepage/adminhtml_homepage_edit_tab_general')->toHtml(),
			'active'	=> $active == 'general_tab' ? true : false
        ));
		
        // Check is single store mode
        if (!Mage::app()->isSingleStoreMode()) 
		{
			// Add the store tab
			$this->addTab('store_tab', array(
				'label'     => Mage::helper('homepage')->__('Store'),
				'title'     => Mage::helper('homepage')->__('Store'),
				'content'   => $this->getLayout()->createBlock('homepage/adminhtml_homepage_edit_tab_store')->toHtml(),
				'active'	=> $active == 'store_tab' ? true : false
			));
        }

        // Add the media tab
        $this->addTab('media_tab', array(
            'label' => Mage::helper('homepage')->__('Media'),
            'title' => Mage::helper('homepage')->__('Media'),
            'content' => $this->getLayout()->createBlock('homepage/adminhtml_homepage_edit_tab_media')->toHtml(),
            'active' => $active == 'media_tab' ? true : false
        ));


        // Add the design tab
        $this->addTab('design_tab', array(
            'label' => Mage::helper('homepage')->__('Design'),
            'title' => Mage::helper('homepage')->__('Design'),
            'content' => $this->getLayout()->createBlock('homepage/adminhtml_homepage_edit_tab_design')->toHtml(),
            'active' => $active == 'design_tab' ? true : false
        ));
		
        return parent::_beforeToHtml();
    }
	
}