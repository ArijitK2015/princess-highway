<?php

/**
 * Class FactoryX_Instagram_Block_Adminhtml_Instagram
 */
class FactoryX_Instagram_Block_Adminhtml_Instagram extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_instagram';
        $this->_blockGroup = 'instagram';
        $this->_headerText = Mage::helper('instagram')->__('Images Lists Manager');
        $this->_addButtonLabel = Mage::helper('instagram')->__('Add List');
        parent::__construct();
    }

}