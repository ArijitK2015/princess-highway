<?php

/**
 * Class FactoryX_ImageCdn_Block_Adminhtml_Cachedb_Edit
 * This is the edit form parent block
 */
class FactoryX_ImageCdn_Block_Adminhtml_Cachedb_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     *	Constructor for the Edit page
     */
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'factoryx_imagecdn';
        $this->_controller = 'adminhtml_cachedb';
        // Add both save and delete buttons
        $this->_updateButton('save', 'label', Mage::helper('imagecdn')->__('Warm those cold images'));
        $this->_removeButton('delete');
    }

}