<?php

/**
 * Class FactoryX_AWAdvancedreportsMod_Block_Advanced_Region
 */
class FactoryX_AWAdvancedreportsMod_Block_Advanced_Region extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     *
     */
    public function __construct()
    {
        $this->_blockGroup = 'awadvancedreportsmod';
        $this->_controller = 'advanced_region';
        $this->_headerText = Mage::helper('advancedreports')->__('Sales by Region');
        parent::__construct();
        $this->_removeButton('add');
    }
}
