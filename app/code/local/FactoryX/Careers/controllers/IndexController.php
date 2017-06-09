<?php
class FactoryX_Careers_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function listAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function viewAction()
    {
        $this->loadLayout();
        try {
            // Load the career
            $careerId = $this->getRequest()->getParam('id');
            $career = Mage::getModel('careers/careers')->load($careerId);

            // In order to get the position so we can set the head title
            $careertTitle = $career->getPosition();
            $this->getLayout()->getBlock('head')->setTitle($careertTitle);
        }
        catch (Exception $e) {
            Mage::helper('careers')->log($this->__('Exception caught in %s under % with message: %s', __FILE__, __FUNCTION__, $e->getMessage()));
            Mage::getSingleton('core/session')->addException($e, $this->__('There was a problem loading the career'));
            $this->_redirectReferer();
            return;
        }
        $this->renderLayout();
    }

}