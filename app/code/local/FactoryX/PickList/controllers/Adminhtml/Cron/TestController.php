<?php
/**

*/
class FactoryX_PickList_Adminhtml_Cron_TestController extends Mage_Adminhtml_Controller_Action {

    private $_helper;

    protected $KNOWN_ERRORS = array(
        "/name/" => "info"
    );

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/config/picklist');
    }

    /*
    test dailyGeneratePickList
    */
    public function testDailyGeneratePickListAction() {
        $msg = "";
        $this->_helper = Mage::helper('picklist');

        try {
            $currModule = Mage::app()->getRequest()->getModuleName();
            $this->_helper->log(sprintf("running %s create cron...", $currModule));

            $websiteModel = Mage::app()->getWebsite($this->getRequest()->getParam('website'));
            $resource = Mage::getModel('picklist/observer');

            $jobId = $resource->dailyGeneratePickList(null, $test = 1);
            $this->_helper->log($this->_helper->__("Complete"));

            $routeName = "adminhtml/log/job/view";
            $url1 = Mage::helper("adminhtml")->getUrl($routeName, array("job_id" => $jobId));

            $msg = $msg . $this->_helper->__(sprintf("Cron job complete, click <a target='_blank' rel='noopener noreferrer' href='%s/%s'>here</a> for details", $url1, $jobId));
            Mage::getSingleton('adminhtml/session')->addSuccess($msg);
        }
        catch(Exception $ex) {
            $msg = $msg . $this->_helper->__(sprintf("test failed! %s", $ex->getMessage()));
            Mage::getSingleton('adminhtml/session')->addError($msg);
        }

        $this->_redirectReferer();
    }

    /*
    test dailyOutputDirPurge
    */
    public function testDailyOutputDirPurgeAction() {
        $msg = "";
        $this->_helper = Mage::helper('picklist');

        try {
            $currModule = Mage::app()->getRequest()->getModuleName();
            $this->_helper->log(sprintf("running %s delete cron...", $currModule));

            $websiteModel = Mage::app()->getWebsite($this->getRequest()->getParam('website'));
            $resource = Mage::getModel('picklist/observer');
            
            $jobId = $resource->dailyOutputDirPurge(null, $test = 1);
            $this->_helper->log($this->_helper->__("Complete"));

            $routeName = "adminhtml/log/job/view";
            $url1 = Mage::helper("adminhtml")->getUrl($routeName, array("job_id" => $jobId));

            $msg = $msg . $this->_helper->__(sprintf("Cron job complete, click <a target='_blank' rel='noopener noreferrer' href='%s/%s'>here</a> for details", $url1, $jobId));
            Mage::getSingleton('adminhtml/session')->addSuccess($msg);
        }
        catch(Exception $ex) {
            $msg = $msg . $this->_helper->__(sprintf("test failed! %s", $ex->getMessage()));
            Mage::getSingleton('adminhtml/session')->addError($msg);
        }

        $this->_redirectReferer();
    }

    /**
     * @param $message
     * @return bool
     */
    private function knowError($message) {

        foreach($this->KNOWN_ERRORS as $known => $help) {
            if (preg_match($known, $message)) {
                return $help;
            }
        }
        return false;
    }

}

