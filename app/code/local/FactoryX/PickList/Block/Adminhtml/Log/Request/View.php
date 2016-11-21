<?php
/**
 */

class FactoryX_PickList_Block_Adminhtml_Log_Request_View extends Mage_Catalog_Block_Product_Abstract {

    /**
    */
    public function __construct() {
        parent::__construct();
        //Mage::helper('picklist')->log(sprintf("%s->params=%s", __METHOD__, print_r($this->getRequest()->getParams(), true)) );
        $this->setTemplate('factoryx/picklist/log/request/view.phtml');
        $this->setRequestId($this->getRequest()->getParam('request_id', false));
    }

    /**
    */
    public function getRequestData() {
        
        if ($this->getRequestId()) {
            return Mage::getModel('picklist/picklist_log_request')->load($this->getRequestId());
        }
        else {
            throw new Exception("No Request Id given");
        }
    }

    /**
    back url
    */
    public function getBackUrl() {
        return Mage::helper('adminhtml')->getUrl('*/adminhtml_log_request');
    }
}
