<?php

/**
 * Class FactoryX_CustomerSurvey_Block_Adminhtml_Survey_Edit_Tab_Questions
 */
class FactoryX_CustomerSurvey_Block_Adminhtml_Survey_Edit_Tab_Questions extends Mage_Adminhtml_Block_Widget
{

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('factoryx/customersurvey/questions.phtml');
    }

    /**
     * @return bool
     */
    public function isReadOnly() {
		return false;	
	}
	
	
	protected function _prepareLayout()
    {
        $this->setChild('delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('customersurvey')->__('Delete Option'),
                    'class' => 'delete delete-product-option '
                ))
        );

        return parent::_prepareLayout();
    }


    /**
     * @return mixed
     */
    public function getAddButtonId()
    {
        $buttonId = $this->getLayout()
                ->getBlock('admin.product.options')
                ->getChild('add_button')->getId();
        return $buttonId;
    }

    /**
     * @return mixed
     */
    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    /**
     * @return array
     */
    public function getMyQuestions() {
		$customersurveyId = $this->getRequest()->getParam('id');
		
		if($customersurveyId) {
			$questions  = Mage::getModel('customersurvey/questions')->getCollection()->addFieldToFilter('customersurvey_id', $customersurveyId);
			$questions = $questions->addAttributeToSort('sort_order', 'ASC');
		}
		else
		{
			$questions = array();	
		}
		
		
		return $questions;
		
	}
	
}