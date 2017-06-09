<?php

/**
 * Class FactoryX_CreditmemoReasons_Block_Adminhtml_Reason
 */
class FactoryX_CreditmemoReasons_Block_Adminhtml_Reason extends Mage_Adminhtml_Block_Widget_Grid_Container {

    /**
     *
     */
    public function __construct()
    {
        $this->_blockGroup      = 'creditmemoreasons';
        $this->_controller      = 'adminhtml_reason';
        $this->_headerText = Mage::helper('creditmemoreasons')->__('Credit memo Reasons');
        $this->_addButtonLabel = Mage::helper('creditmemoreasons')->__('Add Reason');
        parent::__construct();
    }
}

