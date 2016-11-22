<?php
/**
 * Who:  Alvin Nguyen
 * When: 3/10/2014
 * Why:  
 */

class FactoryX_ProductPolice_Block_Adminhtml_Item extends Mage_Adminhtml_Block_Widget_Grid_Container{
    public function _construct(){
        parent::_construct();
        $this->_blockGroup = "factoryx_productpolice_adminhtml";
        $this->_controller = "item";
        $this->_headerText = Mage::helper('factoryx_productpolice')->__('FactoryX Product Police');
    }

    /**
     *
     */
    public function __construct(){
        parent::__construct();
        $this->_removeButton('add');
    }
}