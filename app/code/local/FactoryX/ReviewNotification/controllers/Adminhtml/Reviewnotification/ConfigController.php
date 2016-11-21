<?php

class FactoryX_ReviewNotification_Adminhtml_Reviewnotification_ConfigController extends Mage_Adminhtml_Controller_Action {

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/config/reviewnotification');
    }

    /**
     * Manually run notification
     */
    public function runAction() {

        $count = Mage::helper('reviewnotification/notifier')->run();

        //Confirm & redirect
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('%s notifications sent', $count));
        $this->_redirect('adminhtml/system_config/edit', array('section' => 'reviewnotification'));
    }

}