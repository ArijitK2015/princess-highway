<?php

class FactoryX_Contests_Block_Adminhtml_Contests_Draw_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	/**
	 * Constructor
	 */
    public function __construct()
    {
        parent::__construct();
        $this->setId('draw_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('contests')->__('Draw Winner(s)'));
    }
 
	/**
	 * Prepare the HTML before displaying it
	 */
    protected function _beforeToHtml()
    {
		$this->addTab('form_section1', array(
            'label'     => Mage::helper('contests')->__('Winner(s) details'),
            'title'     => Mage::helper('contests')->__('Winner(s) details'),
            'content'   => $this->getLayout()->createBlock('contests/adminhtml_contests_draw_tab_general')->toHtml(),
        ));
		
        return parent::_beforeToHtml();
    }
	
}