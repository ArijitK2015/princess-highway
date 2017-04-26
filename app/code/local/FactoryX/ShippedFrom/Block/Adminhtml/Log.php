<?php

/**
 * Class FactoryX_ShippedFrom_Block_Adminhtml_Log
 */
class FactoryX_ShippedFrom_Block_Adminhtml_Log extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_log';
        $this->_blockGroup = 'shippedfrom';
        $this->_headerText = Mage::helper('shippedfrom')->__('Auspost Cron Logs');
        parent::__construct();
    }
}