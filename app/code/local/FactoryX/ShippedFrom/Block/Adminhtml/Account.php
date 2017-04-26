<?php

/**
 * Class FactoryX_ShippedFrom_Block_Adminhtml_Account
 */
class FactoryX_ShippedFrom_Block_Adminhtml_Account extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_account';
        $this->_blockGroup = 'shippedfrom';
        $this->_headerText = Mage::helper('shippedfrom')->__('Auspost Accounts');
        parent::__construct();
    }
}