<?php

class FactoryX_Homepage_Block_Adminhtml_Homepage_Chooselayout extends Mage_Adminhtml_Block_Widget_Form_Container
{
	/**
	 *	Constructor for the choose layout page
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->_objectId = 'id';
		$this->_blockGroup = 'homepage';
		$this->_controller = 'adminhtml_homepage';
		$this->_mode = 'chooselayout';	// 'edit' by default
		
		// Remove useless buttons
		$this->_removeButton('save');
		$this->_removeButton('delete');
			
		// Add the continue button
		$this->_addButton('continuehomepage', array(
			'label' => Mage::helper('homepage')->__('Continue'),
			'onclick' => 'continuehomepage()',
			'class' => 'save',
				), -100);
		
		// Set the continue URL
		$this->_formScripts[] = "
			chooselayoutForm = new varienForm('chooselayout_form', '');
			function continuehomepage() {
				$(chooselayoutForm.formId).action = '" . $this->getContinueHomePageUrl() . "';
				chooselayoutForm.submit();
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
	 *	Getter for the continue URL
	 */
	public function getContinueHomePageUrl()
    {
        if ($id = $this->getRequest()->getParam('id'))
		{
			return $this->getUrl('*/*/new', array('id' => $id));
		}
		else return $this->getUrl('*/*/new', array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
    }
}