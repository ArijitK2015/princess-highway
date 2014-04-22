<?php

class FactoryX_Lookbook_Block_Adminhtml_Lookbook_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	/**
	 *	Constructor for the tabs of the edit lookbook page
	 */
    public function __construct()
    {
        parent::__construct();
        $this->setId('lookbook_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('lookbook')->__('Lookbook Information'));
    }
 
	/**
	 * Prepare the HTML before displaying it
	 */
    protected function _beforeToHtml()
    {
		// We get the active tab and empty the session 
		$active = Mage::getSingleton('admin/session')->getActiveTab(true);
		
		// We retrieve the data from the session or the registered data
		if (Mage::getSingleton('adminhtml/session')->getLookbookData()) 
		{
			$data = Mage::getSingleton('adminhtml/session')->getLookbookData();
			Mage::getSingleton('adminhtml/session')->setLookbookData(null);
		} 
		elseif (Mage::registry('lookbook_data')) 
		{
			$data = Mage::registry('lookbook_data')->getData();
		}
		
		// Add the general tab
		$this->addTab('general_tab', array(
            'label'     => Mage::helper('lookbook')->__('General Information'),
            'title'     => Mage::helper('lookbook')->__('General Information'),
            'content'   => $this->getLayout()->createBlock('lookbook/adminhtml_lookbook_edit_tab_general')->toHtml(),
			'active'	=> $active == 'general_tab' ? true : false
        ));
		
        // Check is single store mode
        if (!Mage::app()->isSingleStoreMode()) 
		{
			// Add the store tab
			$this->addTab('store_tab', array(
				'label'     => Mage::helper('lookbook')->__('Store'),
				'title'     => Mage::helper('lookbook')->__('Store'),
				'content'   => $this->getLayout()->createBlock('lookbook/adminhtml_lookbook_edit_tab_store')->toHtml(),
				'active'	=> $active == 'store_tab' ? true : false
			));
        }
		
		if ($data['lookbook_type'] == "images")
		{
			// Add the media tab
			$this->addTab('media_tab', array(
				'label'     => Mage::helper('lookbook')->__('Media'),
				'title'     => Mage::helper('lookbook')->__('Media'),
				'content'   => $this->getLayout()->createBlock('lookbook/adminhtml_lookbook_edit_tab_media')->toHtml(),
				'active'	=> $active == 'media_tab' ? true : false
			));
		}
		
		// Add the credits tab
		$this->addTab('credits_tab', array(
            'label'     => Mage::helper('lookbook')->__('Credits'),
            'title'     => Mage::helper('lookbook')->__('Credits'),
            'content'   => $this->getLayout()->createBlock('lookbook/adminhtml_lookbook_edit_tab_credits')->toHtml(),
			'active'	=> $active == 'credits_tab' ? true : false
        ));
		
		// Add the developer tab
		$this->addTab('dev_tab', array(
            'label'     => Mage::helper('lookbook')->__('Developer'),
            'title'     => Mage::helper('lookbook')->__('Developer'),
            'content'   => $this->getLayout()->createBlock('lookbook/adminhtml_lookbook_edit_tab_developer')->toHtml(),
			'active'	=> $active == 'dev_tab' ? true : false
        ));
		
        return parent::_beforeToHtml();
    }
	
}