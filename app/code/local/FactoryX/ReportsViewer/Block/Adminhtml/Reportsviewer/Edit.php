<?php

/**
 * Class FactoryX_Reportsviewer_Block_Adminhtml_Reportsviewer_Edit
 * This is the view page parent block
 */
class FactoryX_Reportsviewer_Block_Adminhtml_Reportsviewer_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     *	Constructor for the Edit page
     */
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'reportsviewer';
        $this->_controller = 'adminhtml_reportsviewer';
        $this->_updateButton('delete', 'label', Mage::helper('reportsviewer')->__('Delete Report File'));
        // Remove the save button as we do not deal with editable data
        $this->_removeButton('save');
    }

    /**
     *	Getter for the header text
     */
    public function getHeaderText()
    {
        return Mage::helper('reportsviewer')->__('Viewing Report');
    }

}