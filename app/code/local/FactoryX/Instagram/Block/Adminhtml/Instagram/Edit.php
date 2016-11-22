<?php

/**
 * Class FactoryX_Instagram_Block_Adminhtml_Instagram_Edit
 */
class FactoryX_Instagram_Block_Adminhtml_Instagram_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     *	Constructor for the Edit page
     */
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'instagram';
        $this->_controller = 'adminhtml_instagram';
        $this->_updateButton('save', 'label', Mage::helper('instagram')->__('Save List'));
        $this->_updateButton('delete', 'label', Mage::helper('instagram')->__('Delete List'));

        // Add the Save and Continue button
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('instagram')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save'
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }";
    }

    /**
     *	Getter for the header text
     */
    public function getHeaderText()
    {
        if( Mage::registry('instagramlist_data') && Mage::registry('instagramlist_data')->getId() )
        {
            return Mage::helper('instagram')->__("Editing List");
        }
        else
        {
            return Mage::helper('instagram')->__('Add List');
        }
    }

}