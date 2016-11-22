<?php

/**
 * Class FactoryX_Instagram_Block_Adminhtml_Instagram_Edit_Tabs
 */
class FactoryX_Instagram_Block_Adminhtml_Instagram_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     *	Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('instagram_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('instagram')->__('List Information'));
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
            'label'     => Mage::helper('instagram')->__('General Information'),
            'title'     => Mage::helper('instagram')->__('General Information'),
            'content'   => $this->getLayout()->createBlock('instagram/adminhtml_instagram_edit_tab_general')->toHtml(),
            'active'	=> $active == 'general_tab' ? true : false
        ));

        // Add the cars tab
        $this->addTab('approved_tab', array(
            'label'     => Mage::helper('instagram')->__('Approved picture(s)'),
            'title'     => Mage::helper('instagram')->__('Approved picture(s)'),
            'content'   => $this->getLayout()->createBlock('instagram/adminhtml_instagram_edit_tab_approved')->toHtml(),
            'active'	=> $active == 'approved_tab' ? true : false
        ));

        // Add the uploads tab
        $this->addTab('new_tab', array(
            'label'     => Mage::helper('instagram')->__('New picture(s)'),
            'title'     => Mage::helper('instagram')->__('New picture(s)'),
            'content'   => $this->getLayout()->createBlock('instagram/adminhtml_instagram_edit_tab_new')->toHtml(),
            'active'	=> $active == 'new_tab' ? true : false
        ));

        return parent::_beforeToHtml();
    }

}