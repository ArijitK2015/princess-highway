<?php

/**
 * Class FactoryX_CategoryBanners_Block_Adminhtml_Categorybanners_Edit
 * This is the edit form parent block
 */
class FactoryX_CategoryBanners_Block_Adminhtml_Categorybanners_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     *	Constructor for the Edit page
     */
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'categorybanners';
        $this->_controller = 'adminhtml_categorybanners';
        // Add both save and delete buttons
        $this->_updateButton('save', 'label', Mage::helper('categorybanners')->__('Save Banner'));
        $this->_updateButton('delete', 'label', Mage::helper('categorybanners')->__('Delete Banner'));

        // Add the Save and Continue button
        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('categorybanners')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
        ), -100);

        // Save & Continue JS
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }";

        // Add some JS to hide/show dates
        $this->_formScripts[] = "
			Event.observe(window, 'load', function(){
				if ($('category_text'))
				{
				    document.getElementById('category_id').value = $('category_text').value;
				}
			});";
    }

    /**
     *	Getter for the header text
     */
    public function getHeaderText()
    {
        if( Mage::registry('banner_data') && Mage::registry('banner_data')->getId() )
        {
            return Mage::helper('categorybanners')->__("Editing Banner");
        }
        else
        {
            return Mage::helper('categorybanners')->__('Add Banner');
        }
    }

}