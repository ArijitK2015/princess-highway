<?php

/**
 * Class FactoryX_ShippedFrom_Adminhtml_Shippedfrom_Cron_TestController
 */
class FactoryX_ShippedFrom_Adminhtml_Shippedfrom_Cron_TestController extends Mage_Adminhtml_Controller_Action {

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/config/shippedfrom');
    }

    /*
    test storeSalesReport
    */
    public function testStoreSalesReportAction()
    {
        $msg = "";

        try {
            $websiteModel = Mage::app()->getWebsite($this->getRequest()->getParam('website'));
            $resource = Mage::getModel('shippedfrom/observer');
            
            $jobId = $resource->storeSalesReport(null, $test = 1);
            
            $msg = "Complete";
            Mage::helper('shippedfrom')->log(Mage::helper('shippedfrom')->__($msg));
            Mage::getSingleton('adminhtml/session')->addSuccess($msg);            
        }
        catch(Exception $ex) {
            $msg = $msg . Mage::helper('shippedfrom')->__(sprintf("Test failed! %s", $ex->getMessage()));
            Mage::getSingleton('adminhtml/session')->addError($msg);
        }

        $this->_redirectReferer();
    }

}

