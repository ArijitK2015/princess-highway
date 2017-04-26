<?php

/**
 * Class FactoryX_ShippedFrom_Block_Adminhtml_Account_Edit
 * This is the edit form parent block
 */
class FactoryX_ShippedFrom_Block_Adminhtml_Account_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Constructor for the Edit page
     */
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'shippedfrom';
        $this->_controller = 'adminhtml_account';
        // Add both save and delete buttons
        $this->_updateButton('save', 'label', Mage::helper('shippedfrom')->__('Save Account'));
        $this->_updateButton('delete', 'label', Mage::helper('shippedfrom')->__('Delete Account'));

        // Add the Save and Continue button
        $this->_addButton(
            'saveandcontinue',
            array(
                'label' => Mage::helper('shippedfrom')->__('Save And Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class' => 'save',
            ),
            -100
        );

        // Save & Continue JS
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }";
    }

    /**
     * Getter for the header text
     */
    public function getHeaderText()
    {
        if (Mage::registry('account_data') && Mage::registry('account_data')->getId()) {
            return Mage::helper('shippedfrom')->__("Editing Account");
        } else {
            return Mage::helper('shippedfrom')->__('Add Account');
        }
    }

}