<?php

/**
 * Class FactoryX_CustomerSurvey_Block_Adminhtml_Survey_Results
 */
class FactoryX_CustomerSurvey_Block_Adminhtml_Survey_Results extends Mage_Adminhtml_Block_Widget_Form_Container
{
	/**
	 *
     */
	public function __construct()
	{
		parent::__construct();
		$this->_objectId = 'customersurvey_id';
		$this->_blockGroup = 'customersurvey';
		$this->_controller = 'adminhtml_survey';
	}
		
	public function getHeaderText()
	{
		if( Mage::registry('customersurvey_data') && Mage::registry('customersurvey_data')->getId() ) {
			return Mage::helper('customersurvey')->__("Results");
		} else {
			return Mage::helper('customersurvey')->__('');
		}
	}
}