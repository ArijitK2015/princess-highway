<?php
 
class Xigmapro_Jobs_Block_Adminhtml_Jobs extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_Jobs';
        $this->_blockGroup = 'Jobs';
        $this->_headerText = Mage::helper('Jobs')->__('Jobs Manager');
        $this->_addButtonLabel = Mage::helper('Jobs')->__('Add Jobs');
        parent::__construct();
    }
}