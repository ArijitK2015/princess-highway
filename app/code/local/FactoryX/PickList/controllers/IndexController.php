<?php
/**
server pick list documents
*/

class FactoryX_PickList_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        Mage::helper('picklist')->log(__METHOD__);
    }

    /**
    
    file_path=picklists%2Fe9a3c957fb06bdb2461cf307e3b4b258%2F20140813_b02_pick_list.pdf
    */
    public function getAction() {

        $params = $this->getRequest()->getParams();
        //Mage::helper('picklist')->log(sprintf("%s->params=%s", __METHOD__, var_export($params, true)));

        //$params['email']
        //$params['job_id']

        $filePath = sprintf("%s/%s", Mage::getBaseDir('media'), $params['file_path']);
        //Mage::helper('picklist')->log(sprintf("filePath=%s", $filePath));

        $returnStatus = 404;
        if (is_file($filePath)) {
            $returnStatus = 200;
        }
        
        // log request
        Mage::helper('picklist')->logPickListRequest(
            Mage::helper('core/http')->getHttpUserAgent(),
            json_encode($params),
            $returnStatus
        );
        
        if (is_file($filePath)) {
            
            $parts = pathinfo($filePath);
            //$this->getResponse()->setHeader('Cache-Control', 'public');
            //$this->getResponse()->setHeader('Content-Type', 'application/pdf');
            //$this->getResponse()->setHeader('Content-Disposition', sprintf("'attachment; filename=\"%s\"'", $parts['basename']));

            header('Cache-Control: public'); // needed for i.e.
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $parts['basename'] . '"');
            
            readfile($filePath);
            exit();
        }
        else {
            Mage::helper('picklist')->log(sprintf("returnStatus=%s", $returnStatus));
            
            $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
            $this->getResponse()->setHeader('Status', '404 File not found');
            $this->_forward('defaultNoRoute');
        }
    }

}