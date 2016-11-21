<?php
/**

*/
class FactoryX_StoreLocator_Adminhtml_Cron_RunController extends Mage_Adminhtml_Controller_Action {

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/config/ustorelocator');
    }

    /*
    test dailyGeneratePickList
    */
    public function runStoreResolverAction() {
        $msg = "";
        $_helper = Mage::helper('ustorelocator');

        try {
            $currModule = Mage::app()->getRequest()->getModuleName();
            $_helper->log(sprintf("running %s create cron...", $currModule));

            $websiteModel = Mage::app()->getWebsite($this->getRequest()->getParam('website'));

            $observer = Mage::getModel('ustorelocator/observer');
            $observer->storeResolver();
            
            $msg = "Cron job complete";
            $_helper->log($msg);
            Mage::getSingleton('adminhtml/session')->addSuccess($msg);
        }
        catch(Exception $ex) {
            $msg = $_helper->__(sprintf("job failed! %s", $ex->getMessage()));
            Mage::getSingleton('adminhtml/session')->addError($msg);
        }
        $this->_redirectReferer();
    }


}

