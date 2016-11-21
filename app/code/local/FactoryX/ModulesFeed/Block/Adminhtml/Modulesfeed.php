<?php

/**
 * Class FactoryX_ModulesFeed_Block_Adminhtml_Modulesfeed
 */
class FactoryX_ModulesFeed_Block_Adminhtml_Modulesfeed extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_modulesfeed';
        $this->_blockGroup = 'modulesfeed';
        $this->_headerText = Mage::helper('modulesfeed')->__('Modules Details');
        parent::__construct();
        $this->_removeButton('add');
    }
}