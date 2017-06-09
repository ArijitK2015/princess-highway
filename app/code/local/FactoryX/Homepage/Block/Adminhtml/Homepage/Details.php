<?php

/**
 * Class FactoryX_Homepage_Block_Adminhtml_Homepage_Details
 */
class FactoryX_Homepage_Block_Adminhtml_Homepage_Details extends Mage_Adminhtml_Block_Widget_Form_Container
{
	/**
	 *	Constructor for the details page
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->_objectId = 'id';
		$this->_blockGroup = 'homepage';
		$this->_controller = 'adminhtml_homepage';
		$this->_mode = 'details'; // edit by default
		
		// Remove useless buttons
		$this->_removeButton('save');
		$this->_removeButton('delete');
			
		// Add the continue button
		$this->_addButton('chooselayout', array(
			'label' => Mage::helper('homepage')->__('Choose Layout'),
			'onclick' => 'chooselayout()',
			'class' => 'save',
				), -100);
		
		// Add the choose layout URL
		$this->_formScripts[] = "
			detailsForm = new varienForm('details_form', '');
			function chooselayout() {
				$(detailsForm.formId).action = '" . $this->getChooseLayoutUrl() . "';
				detailsForm.submit();
			}
		";
	}
	
	/**
	 *	Getter for the header text
	 */
	public function getHeaderText()
	{
		return Mage::helper('homepage')->__('Home Page');
	}
	
	/**
	 *	Getter for the choose layout URL
	 */
	public function getChooseLayoutUrl()
    {
		if ($id = $this->getRequest()->getParam('id'))
		{
			return $this->getUrl('*/*/chooselayout', array('id' => $id));
		}
		else return $this->getUrl('*/*/chooselayout', array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
    }
}