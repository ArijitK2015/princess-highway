<?php

/**
 * Class FactoryX_BirthdayGift_Adminhtml_BirthdaygiftController
 */
class FactoryX_BirthdayGift_Adminhtml_BirthdaygiftController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/config/bdayconfig');
    }

    /**
     * Manually send the notifications
     *
     * @return void
     */
    public function sendAction()
    {
		$model = Mage::getModel('birthdaygift/observer');
		$model->sendBirthdayEmail(null, Mage::helper('birthdaygift')->getTestEmail());
		
        $result = 1;
        Mage::app()->getResponse()->setBody($result);
    }
}