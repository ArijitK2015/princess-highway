<?php
 
class Xigmapro_Jobs_Block_Adminhtml_Jobs_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
 
    public function __construct()
    {
        parent::__construct();
        $this->setId('Jobs_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('Jobs')->__('Job Information'));
    }
 
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('Jobs')->__('Job Information'),
            'title'     => Mage::helper('Jobs')->__('Job Information'),
            'content'   => $this->getLayout()->createBlock('Jobs/adminhtml_Jobs_edit_tab_form')->toHtml(),
        ));
       
        return parent::_beforeToHtml();
    }
}