<?php

/**
 * Class FactoryX_Homepage_Block_Adminhtml_Homepage_Edit
 */
class FactoryX_Homepage_Block_Adminhtml_Homepage_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	/**
	 *	Constructor for the Edit page
	 */
	public function __construct()
	{
		parent::__construct();
		$this->_objectId = 'id';
		$this->_blockGroup = 'homepage';
		$this->_controller = 'adminhtml_homepage';
		$this->_updateButton('save', 'label', Mage::helper('homepage')->__('Save'));
		$this->_updateButton('delete', 'label', Mage::helper('homepage')->__('Delete'));

		// If we're editing (not creating), we add the preview button
		if ($this->getRequest()->getParam('id'))
		{
			// Add the Preview button
			$this->_addButton('preview', array(
				'label' => Mage::helper('homepage')->__('Preview'),
				'onclick' => 'preview()',
				'class' => 'save',
			), -100);

			$this->_formScripts[] = "
				function preview(){
					var newWnd = window.open('" . $this->getPreviewUrl() . "', '_blank');
					newWnd.opener = null;
				}";

			// Add the Duplicate button
			$this->_addButton('preview', array(
				'label' => Mage::helper('homepage')->__('Duplicate'),
				'onclick' => 'setLocation(\'' . $this->getDuplicateUrl() . '\')',
				'class' => 'add',
			), -100);
		}

		// Add the Save and Continue button
		$this->_addButton('saveandcontinue', array(
			'label' => Mage::helper('homepage')->__('Save And Continue Edit'),
			'onclick' => 'saveAndContinueEdit()',
			'class' => 'save',
		), -100);

		$this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }";

		$data = Mage::registry('homepage_data');

		// Add some JS to hide/show dates
		for ($i = 1; $i <= $data['amount'];$i++)
		{
			$this->_formScripts[] = "
				Event.observe(window, 'load', function(){
					if ($('type_$i').selectedIndex != 0)
					{
					}
					else
					{
						$('chooserblock_id_$i').parentNode.parentNode.hide();
						$('chooserblock_id_$i').parentNode.parentNode.previous().hide();
					}
				});
				Event.observe($('type_$i'),'change', function(){
					if ($('type_$i').selectedIndex != 0)
					{
						$('chooserblock_id_$i').parentNode.parentNode.show();
						$('chooserblock_id_$i').parentNode.parentNode.previous().show();
					}
					else
					{
						$('chooserblock_id_$i').parentNode.parentNode.hide();
						$('chooserblock_id_$i').parentNode.parentNode.previous().hide();
					}
				});";
		}
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
				jQuery('#homepage_tabs_design_tab_content .hor-scroll').css('overflow','visible');
			});
        ";
	}

	/**
	 *	Getter for the header text
	 */
	public function getHeaderText()
	{
		if( Mage::registry('homepage_data') && Mage::registry('homepage_data')->getId() )
		{
			return Mage::helper('homepage')->__("Editing Home Page");
		}
		else
		{
			return Mage::helper('homepage')->__('Add Home Page');
		}
	}

	/**
	 *	Getter for the preview URL
	 */
	public function getPreviewUrl()
	{
		return Mage::getUrl('homepage/index/preview', array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
	}

	/**
	 *	Getter for the duplicate URL
	 */
	public function getDuplicateUrl()
	{
		return $this->getUrl('*/*/duplicate', array('_current'=>true));
	}
}