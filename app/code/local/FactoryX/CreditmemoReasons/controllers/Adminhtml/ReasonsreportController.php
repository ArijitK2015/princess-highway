<?php

/**
 * Class FactoryX_CreditmemoReasons_Adminhtml_ReasonsreportController
 */
class FactoryX_CreditmemoReasons_Adminhtml_ReasonsreportController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/salesroot/reasons');
    }

    /**
     *
     */
    public function indexAction()
    {
        // Get the session
        $session = Mage::getSingleton('core/session');

        // Get the dates
        $_from    = $this->getRequest()->getParam('from');
        $_to	  = $this->getRequest()->getParam('to');
        $_addSkus = $this->getRequest()->getParam('addsku');
        $_reason = $this->getRequest()->getParam('reason');

        // Use the session to manage the dates
        if ($_from != "") $session->setReasonreportsFrom($_from);
        if ($_to != "") $session->setReasonreportsTo($_to);
        if ($_addSkus == "on") {
            $session->setReasonreportsAddsku(true);
        }
        else {
            $session->setReasonreportsAddsku(false);
        }
        if ($_reason) {
            $session->setReasonreportsReason($_reason);
        }
        else {
            $session->unsReasonreportsReason();
        }

        $this->loadLayout()
            ->_setActiveMenu('report/salesroot/reasons')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Credit Memo Reasons Report'), Mage::helper('adminhtml')->__('Credit Memo Reasons Report'))
            ->_addContent( $this->getLayout()->createBlock('creditmemoreasons/adminhtml_report') )
            ->renderLayout();
    }
    
    /**
     *
     */
    public function exportCsvAction() {
		$fileName = sprintf('%s-creditmemoreasons.csv', date("Ymd"));
 
        // FactoryX_CreditmemoReasons_Block_Adminhtml_Report_Grid
		$content = $this->getLayout()
			->createBlock('creditmemoreasons/adminhtml_report_grid')
			->getCsvFile();
 
		$this->_prepareDownloadResponse($fileName, $content);
	}

    /**
     *
     */
    public function exportPdfAction() {
        $fileName = sprintf('%s-creditmemoreasons.pdf', date('Ymd'));

        $content = $this->getLayout()
            ->createBlock('creditmemoreasons/adminhtml_report_grid')
            ->getPdfFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }
    
}