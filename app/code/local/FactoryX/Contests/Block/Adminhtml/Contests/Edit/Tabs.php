<?php

class FactoryX_Contests_Block_Adminhtml_Contests_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	/**
	 *	Constructor
	 */
    public function __construct()
    {
        parent::__construct();
        $this->setId('contests_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('contests')->__('Contest Information'));
    }
 
	/**
	 * Prepare the HTML before displaying it
	 */
    protected function _beforeToHtml()
    {
		// We get the active tab and empty the session 
		$active = Mage::getSingleton('admin/session')->getActiveTab();
		Mage::getSingleton('admin/session')->setActiveTab(false);
		
		// Add the general tab
		$this->addTab('general_tab', array(
            'label'     => Mage::helper('contests')->__('General Information'),
            'title'     => Mage::helper('contests')->__('General Information'),
            'content'   => $this->getLayout()->createBlock('contests/adminhtml_contests_edit_tab_general')->toHtml(),
			'active'	=> $active == 'general_tab' ? true : false
        ));
		
        // Check is single store mode
        if (!Mage::app()->isSingleStoreMode()) 
		{
			// Add the store tab
			$this->addTab('store_tab', array(
				'label'     => Mage::helper('contests')->__('Store'),
				'title'     => Mage::helper('contests')->__('Store'),
				'content'   => $this->getLayout()->createBlock('contests/adminhtml_contests_edit_tab_store')->toHtml(),
				'active'	=> $active == 'store_tab' ? true : false
			));
        }
		
		// Add the media tab
		$this->addTab('media_tab', array(
            'label'     => Mage::helper('contests')->__('Media'),
            'title'     => Mage::helper('contests')->__('Media'),
            'content'   => $this->getLayout()->createBlock('contests/adminhtml_contests_edit_tab_media')->toHtml(),
			'active'	=> $active == 'media_tab' ? true : false
        ));
		
		// Add the list tab
		$this->addTab('list_tab', array(
            'label'     => Mage::helper('contests')->__('List'),
            'title'     => Mage::helper('contests')->__('List'),
            'content'   => $this->getLayout()->createBlock('contests/adminhtml_contests_edit_tab_list')->toHtml(),
			'active'	=> $active == 'list_tab' ? true : false
        ));
		
		// Add the terms and conditions tab
		$this->addTab('terms_tab', array(
            'label'     => Mage::helper('contests')->__('Terms &amp; Conditions'),
            'title'     => Mage::helper('contests')->__('Terms &amp; Conditions'),
            'content'   => $this->getLayout()->createBlock('contests/adminhtml_contests_edit_tab_terms')->toHtml(),
			'active'	=> $active == 'terms_tab' ? true : false
        ));
		
		// Add the competition tab
		$this->addTab('competition_tab', array(
            'label'     => Mage::helper('contests')->__('Competition'),
            'title'     => Mage::helper('contests')->__('Competition'),
            'content'   => $this->getLayout()->createBlock('contests/adminhtml_contests_edit_tab_competition')->toHtml(),
			'active'	=> $active == 'competition_tab' ? true : false
        ));
		
		// Add the popup tab
		$this->addTab('popup_tab', array(
            'label'     => Mage::helper('contests')->__('Popup'),
            'title'     => Mage::helper('contests')->__('Popup'),
            'content'   => $this->getLayout()->createBlock('contests/adminhtml_contests_edit_tab_popup')->toHtml(),
			'active'	=> $active == 'popup_tab' ? true : false
        ));
		
		// Add the winners tab
		$this->addTab('winners_tab', array(
            'label'     => Mage::helper('contests')->__('Winner(s)'),
            'title'     => Mage::helper('contests')->__('Winner(s)'),
            'content'   => $this->getLayout()->createBlock('contests/adminhtml_contests_edit_tab_winners')->toHtml(),
			'active'	=> $active == 'winners_tab' ? true : false
        ));
		
        return parent::_beforeToHtml();
    }
	
}