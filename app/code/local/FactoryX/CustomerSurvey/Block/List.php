<?php

/**
 * Class FactoryX_CustomerSurvey_Block_List
 */
class FactoryX_CustomerSurvey_Block_List extends Mage_Core_Block_Template
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle($this->__('List Customer Surveys'));
        }
    }

    /**
     * @return mixed
     */
    public function getSurveys()
	{ 
		return  Mage::getModel('customersurvey/survey')->getCollection();
	}

    /**
     * @param $customersurveyId
     * @return mixed
     */
    public function questionsForSurvey($customersurveyId)
	{
		return Mage::getModel('customersurvey/questions')->getCollection()->addFieldToFilter('customersurvey_id', $customersurveyId)->count();
	}

}