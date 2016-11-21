<?php

/**
 * Controller for the Log viewing operations
 *
 */


class FactoryX_PickList_Adminhtml_Log_JobController extends Mage_Adminhtml_Controller_Action {

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('factoryx_menu/picklist/log/log_job');
    }

    /**
     * @return $this
     */
    protected function _initAction() {
        
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('factoryx_menu');
            /*
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('System'), Mage::helper('adminhtml')->__('System'))
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Tools'), Mage::helper('adminhtml')->__('Tools'))
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Email Log'), Mage::helper('adminhtml')->__('Email Log'));
            */
        return $this;
    }    
        
    public function indexAction() {
        
          $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('picklist/adminhtml_log_job'))
            ->renderLayout();
        
    }    
    
    /**
    view job log item
    */
    public function viewAction() {
        //Mage::helper('picklist')->log(sprintf("%s->params=%s", __METHOD__, print_r($this->getRequest()->getParams(), true)) );
        
        // FactoryX_PickList_Block_Adminhtml_Log_Job_View
        $block = $this->getLayout()->createBlock('picklist/adminhtml_log_job_view');
        $this->_initAction()->_addContent($block)->renderLayout();
    }    
    
} 
