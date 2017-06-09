<?php

/**
 * Class FactoryX_CustomGrids_Block_Adminhtml_Customgrid
 */
class FactoryX_CustomGrids_Block_Adminhtml_Customgrid extends Mage_Adminhtml_Block_Widget_Grid_Container {

    /**
     *
     */
    public function __construct()
    {
        $this->_blockGroup      = 'customgrids';
        $this->_controller      = 'adminhtml_customgrid';
        $this->_headerText = Mage::helper('customgrids')->__('Custom Grids Attribute');
        $this->_addButtonLabel = Mage::helper('customgrids')->__('Add Attribute');

        $this->_addButton('remove_column', array(
            'label'     => Mage::helper('customgrids')->__('Remove Native Column'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/edit/',array('remove' => true)) . '\')',
            'class'     => 'edit',
        ));

        parent::__construct();
    }
}

