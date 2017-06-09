<?php

/**
 * Class FactoryX_CustomerSurvey_Block_Adminhtml_Survey_Results_Tabs
 */
class FactoryX_CustomerSurvey_Block_Adminhtml_Survey_Results_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('customersurvey_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('customersurvey')->__('Survey Results'));
    }
 
    protected function _beforeToHtml()
    {
		
      $this->addTab('form_section1', array(
            'label'     => Mage::helper('customersurvey')->__('General'),
            'title'     => Mage::helper('customersurvey')->__('General'),
            'content'   => $this->getLayout()->createBlock('customersurvey/adminhtml_survey_results_tab_general')->toHtml(),
        ));
		
		$this->addTab('form_section2', array(
            'label'     => Mage::helper('customersurvey')->__('Graphical'),
            'title'     => Mage::helper('customersurvey')->__('Graphical'),
            'content'   => $this->getLayout()->createBlock('customersurvey/adminhtml_survey_results_tab_graphical')->toHtml(),
        ));
       
       return parent::_beforeToHtml();
    }
	
}