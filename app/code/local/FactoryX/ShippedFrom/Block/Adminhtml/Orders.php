<?php

/**
 * Class FactoryX_ShippedFrom_Block_Adminhtml_Orders
 */
class FactoryX_ShippedFrom_Block_Adminhtml_Orders extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_orders';
        $this->_blockGroup = 'shippedfrom';
        $this->_headerText = Mage::helper('shippedfrom')->__('Auspost Orders');
        parent::__construct();
        $this->_removeButton('add');
    }
}