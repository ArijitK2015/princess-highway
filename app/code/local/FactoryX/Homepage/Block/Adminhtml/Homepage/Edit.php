<?php

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
					window.open('" . $this->getPreviewUrl() . "', '_blank');
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
			
		// Add some JS to hide/show dates
		$this->_formScripts[] = "
			Event.observe(window, 'load', function(){
				if ($('status').selectedIndex != 0)
				{
					$('start_date').parentNode.parentNode.hide();
					$('end_date').parentNode.parentNode.hide();
				}
			});
			Event.observe($('status'),'change', function(){
				if ($('status').selectedIndex != 0)
				{
					$('start_date').value = '';
					$('end_date').value = '';
					$('start_date').parentNode.parentNode.hide();
					$('end_date').parentNode.parentNode.hide();
				}
				else
				{
					$('start_date').parentNode.parentNode.show();
					$('end_date').parentNode.parentNode.show();
				}
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