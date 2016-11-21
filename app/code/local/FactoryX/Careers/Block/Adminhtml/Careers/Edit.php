<?php
 
class FactoryX_Careers_Block_Adminhtml_Careers_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
               
        $this->_objectId = 'id';
        $this->_blockGroup = 'careers';
        $this->_controller = 'adminhtml_careers';

        $this->_updateButton('save', 'label', Mage::helper('careers')->__('Save Career'));
        $this->_updateButton('delete', 'label', Mage::helper('careers')->__('Delete Career'));
    }
 
    public function getHeaderText()
    {
        if( Mage::registry('careers_data') && Mage::registry('careers_data')->getId() ) {
            return Mage::helper('careers')->__("Edit Career '%s'", $this->htmlEscape(Mage::registry('careers_data')->getPosition()));
        } else {
            return Mage::helper('careers')->__('Add Career');
        }
    }
}