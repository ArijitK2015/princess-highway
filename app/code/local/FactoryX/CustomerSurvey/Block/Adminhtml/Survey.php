<?php

/**
 * Class FactoryX_CustomerSurvey_Block_Adminhtml_Survey
 */
class FactoryX_CustomerSurvey_Block_Adminhtml_Survey extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_survey';
        $this->_blockGroup = 'customersurvey';
        $this->_headerText = Mage::helper('customersurvey')->__('Customer Surveys Manager');
        $this->_addButtonLabel = Mage::helper('customersurvey')->__('Add Customer Survey');
        parent::__construct();
    }
}