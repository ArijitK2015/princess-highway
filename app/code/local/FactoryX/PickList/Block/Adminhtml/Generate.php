<?php
/**

other widgets
Mage_Adminhtml_Block_Widget
Mage_Adminhtml_Block_Widget_Grid_Container
*/
class FactoryX_PickList_Block_Adminhtml_Generate extends Mage_Adminhtml_Block_Widget_Container {

    public function __construct() {
        //Mage::helper('picklist')->log(sprintf("%s", __METHOD__));
        $this->_controller = 'adminhtml_generate';
        $this->_blockGroup = 'PickList';
        $this->_headerText = 'Generate Pick List';
        parent::__construct();
    }

    /**
    */
    protected function _toHtml() {
        Mage::helper('picklist')->log(sprintf("%s", __METHOD__));
        $html = parent::_toHtml();
        //$html .= 'Custom message';
        return $html;
    }

    public function getHeaderText() {
        return Mage::helper('PickLIst')->__("Generate Pick List");
    }
}