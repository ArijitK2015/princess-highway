<?php

/**
 * Class FactoryX_Instagram_Block_Adminhtml_Instagram_Log
 */
class FactoryX_Instagram_Block_Adminhtml_Instagram_Log extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_instagram_log';
        $this->_blockGroup = 'instagram';
        $this->_headerText = Mage::helper('instagram')->__('Images Logs Manager');
        parent::__construct();
    }

}