<?php

/**
 * Class FactoryX_ShippedFrom_Block_Adminhtml_Product
 */
class FactoryX_ShippedFrom_Block_Adminhtml_Product extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_product';
        $this->_blockGroup = 'shippedfrom';
        $this->_headerText = Mage::helper('shippedfrom')->__('Auspost Products');
        parent::__construct();
    }
}