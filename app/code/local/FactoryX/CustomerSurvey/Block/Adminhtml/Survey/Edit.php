<?php

/**
 * Class FactoryX_CustomerSurvey_Block_Adminhtml_Survey_Edit
 */
class FactoryX_CustomerSurvey_Block_Adminhtml_Survey_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	/**
	 *
     */
	public function __construct()
	{
		parent::__construct();
		$this->_objectId = 'id';
		$this->_blockGroup = 'customersurvey';
		$this->_controller = 'adminhtml_survey';
		$this->_updateButton('save', 'label', Mage::helper('customersurvey')->__('Save Survey'));
		$this->_updateButton('delete', 'label', Mage::helper('customersurvey')->__('Delete Survey'));
	}

	/**
	 * @return mixed
     */
	public function getHeaderText()
	{
		if( Mage::registry('customersurvey_data') && Mage::registry('customersurvey_data')->getId() ) {
			return Mage::helper('customersurvey')->__("Editing Survey");
		} else {
			return Mage::helper('customersurvey')->__('Add Survey');
		}
	}
}