<?php

class Citybeach_Omnivore_Adminhtml_OmnivoreController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('citybeach_omnivore');
    }

    public function indexAction()
    {
        // TODO: move logic for install/assign here
        $sess = Mage::getSingleton('admin/session');
        $sessUser = $sess->getUser();

        $userId = Mage::helper('adminhtml')->getCurrentUserId();

        $email = $sessUser->getEmail();

        $this->loadLayout();
        $this->renderLayout();

        return $this;
    }
}