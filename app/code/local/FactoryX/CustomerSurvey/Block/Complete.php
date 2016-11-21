<?php

/**
 * Class FactoryX_CustomerSurvey_Block_Complete
 */
class FactoryX_CustomerSurvey_Block_Complete extends Mage_Core_Block_Template
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock)
        {
            $headBlock->setTitle($this->__('Customer Survey'));
        }
    }

    public function getCurrentSurvey()
    {
        $customersurveyId = $this->getRequest()->getParam('id');

        return Mage::getModel('customersurvey/survey')->load($customersurveyId);
    }

    public function getCoupon()
    {
        $coupon = Mage::getModel('salesrule/coupon')->load($this->getCurrentSurvey()->getCode(),'rule_id');
        return $coupon->getCode();
    }

    public function getRule()
    {
        return Mage::getModel('salesrule/rule')->load($this->getCurrentSurvey()->getCode());
    }
}