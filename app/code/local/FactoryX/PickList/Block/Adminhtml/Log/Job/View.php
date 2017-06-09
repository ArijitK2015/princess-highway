<?php
/**
 */

class FactoryX_PickList_Block_Adminhtml_Log_Job_View extends Mage_Catalog_Block_Product_Abstract {

    /**
    */
    public function __construct() {
        parent::__construct();
        //Mage::helper('picklist')->log(sprintf("%s->params=%s", __METHOD__, print_r($this->getRequest()->getParams(), true)) );
        $this->setTemplate('factoryx/picklist/log/job/view.phtml');
        $this->setJobId($this->getRequest()->getParam('job_id', false));
    }

    /**
    */
    public function getJobData() {
        
        if ($this->getJobId()) {
            return Mage::getModel('picklist/picklist_log_job')->load($this->getJobId());
        }
        else {
            throw new Exception("No Job Id given");
        }
    }

    /**
    back url
    */
    public function getBackUrl() {
        return Mage::helper('adminhtml')->getUrl('*/adminhtml_log_job');
    }
}
