<?php
 
class Xigmapro_Jobs_Block_Adminhtml_Jobs_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
               
        $this->_objectId = 'id';
        $this->_blockGroup = 'Jobs';
        $this->_controller = 'adminhtml_Jobs';
 
        $this->_updateButton('save', 'label', Mage::helper('Jobs')->__('Save Job'));
        $this->_updateButton('delete', 'label', Mage::helper('Jobs')->__('Delete Job'));
    }
 
    public function getHeaderText()
    {
        if( Mage::registry('Jobs_data') && Mage::registry('Jobs_data')->getId() ) {
            return Mage::helper('Jobs')->__("Edit Job '%s'", $this->htmlEscape(Mage::registry('Jobs_data')->getPosition()));
        } else {
            return Mage::helper('Jobs')->__('Add Job');
        }
    }
}