<?php

/**
 * Class FactoryX_Lookbook_Block_Adminhtml_Lookbook_Edit
 */
class FactoryX_Lookbook_Block_Adminhtml_Lookbook_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     *	Constructor for the Edit page
     */
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'lookbook';
        $this->_controller = 'adminhtml_lookbook';
        $this->_updateButton('save', 'label', Mage::helper('lookbook')->__('Save Lookbook'));
        $this->_updateButton('delete', 'label', Mage::helper('lookbook')->__('Delete Lookbook'));

        // If we're editing (not creating), we add the change type button
        if ($this->getRequest()->getParam('id'))
        {
            // Add the Preview button
            $this->_addButton('changetype', array(
                'label' => Mage::helper('lookbook')->__('Change Lookbook Type'),
                'onclick' => 'changetype()',
                'class' => 'save',
            ), -100);

            $this->_formScripts[] = "
				function changetype(){
					window.location.href = '" . $this->getChangetypeUrl() . "';
				}";
        }

        // Add the Save and Continue button
        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('lookbook')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }";

        // Add some JS to hide/show options
        $this->_formScripts[] = "
			jQuery(document).ready(function(){
				if(jQuery('#slider_nav_style').val())
				{
					var i = 0;
					jQuery('#slider_nav_dropdown option').each(function(){
						if (jQuery(this).val() == jQuery('#slider_nav_style').val()) return false;
						else i++;
					});
				}
				else
				{
					var i = 0;
				}
				jQuery('#slider_nav_dropdown').ddslick({
					width: 430,
					defaultSelectedIndex: i,
					onSelected: function(selectedData){
						jQuery('#slider_nav_style').val(selectedData.selectedData.value);
					}
				});
				if(jQuery('#slider_pagination_style').val())
				{
					var i = 0;
					jQuery('#slider_pagination_dropdown option').each(function(){
						if (jQuery(this).val() == jQuery('#slider_pagination_style').val()) return false;
						else i++;
					});
				}
				else
				{
					var i = 0;
				}
				jQuery('#slider_pagination_dropdown').ddslick({
					width: 150,
					defaultSelectedIndex: i,
					onSelected: function(selectedData){
						jQuery('#slider_pagination_style').val(selectedData.selectedData.value);
					}
				});
				jQuery('#lookbook_tabs_general_tab_content .hor-scroll').css('overflow','visible');
			});
        ";
    }

    /**
     *	Getter for the header text
     */
    public function getHeaderText()
    {
        if( Mage::registry('lookbook_data') && Mage::registry('lookbook_data')->getId() )
        {
            return Mage::helper('lookbook')->__("Editing Lookbook");
        }
        else
        {
            return Mage::helper('lookbook')->__('Add Lookbook');
        }
    }

    /**
     *	Getter for the preview URL
     */
    public function getChangetypeUrl()
    {
        if ($id = $this->getRequest()->getParam('id'))
        {
            return $this->getUrl('*/*/choosecat', array('id' => $id));
        }
        else return Mage::getUrl('*/*/choosecat', array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
    }
}