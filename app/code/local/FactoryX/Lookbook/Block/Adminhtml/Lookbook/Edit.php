<?php

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
				'label' => Mage::helper('homepage')->__('Change Lookbook Type'),
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
			if ($('show_shop_pix'))
			{
				Event.observe(window, 'load', function(){
					if ($('show_shop_pix').selectedIndex == 0)
					{
						$('shop_pix').parentNode.parentNode.hide();
					}
				});
			
				Event.observe($('show_shop_pix'),'change', function(){
					if ($('show_shop_pix').selectedIndex == 0)
					{
						$('shop_pix').parentNode.parentNode.hide();
					}
					else
					{
						$('shop_pix').parentNode.parentNode.show();
					}
				});
			}
			
			Event.observe(window, 'load', function(){
				if ($('include_in_nav').selectedIndex == 0)
				{
					$('sort_order').parentNode.parentNode.hide();
				}
				
				if ($('show_credits'))
				{
					if ($('show_credits').selectedIndex == 0)
					{
						$('model').parentNode.parentNode.hide();
						$('photography').parentNode.parentNode.hide();
						$('make_up').parentNode.parentNode.hide();
					}
				}
			});
			
			if ($('show_credits'))
			{
				Event.observe($('show_credits'),'change', function(){
					if ($('show_credits').selectedIndex == 0)
					{
						$('model').parentNode.parentNode.hide();
						$('photography').parentNode.parentNode.hide();
						$('make_up').parentNode.parentNode.hide();
					}
					else
					{
						$('model').parentNode.parentNode.show();
						$('photography').parentNode.parentNode.show();
						$('make_up').parentNode.parentNode.show();
					}
				});
			}
			
			Event.observe($('include_in_nav'),'change', function(){
				if ($('include_in_nav').selectedIndex == 0)
				{
					$('sort_order').parentNode.parentNode.hide();
				}
				else
				{
					$('sort_order').parentNode.parentNode.show();
				}
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