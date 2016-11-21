<?php

/**
 * Class FactoryX_CustomReports_Adminhtml_NocrosssellsController
 */
class FactoryX_CustomReports_Adminhtml_NocrosssellsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/products/nocrosssells');
    }

    /**
     *
     */
    public function indexAction()
    {
        // Get the session
        $session = Mage::getSingleton('core/session');

        //Mage::helper('customreports')->log(sprintf("%s->params:%s", __METHOD__, print_r($this->getRequest()->getParams(), true)) );

        if ($this->getRequest()->getParam('sort')) {
            $session->setNocrosssellsSort($this->getRequest()->getParam('sort'));
        }
        else {
            //$session->unsNocrosssellsSort();
            $session->setNocrosssellsSort("name");
        }
        if ($this->getRequest()->getParam('dir')) {
            $session->setNocrosssellsDir($this->getRequest()->getParam('dir'));
        }
        else {
            //$session->unsNocrosssellsSort();
            $session->setNocrosssellsDir("ASC");
        }

        // get params
        $_count = $this->getRequest()->getParam('count');
        //Mage::helper('customreports')->log(sprintf("%s->_count:%s|%s", __METHOD__, $_count, intval($_count)));

        // Use the session to manage the count
        if (strlen($_count) && is_numeric(intval($_count))) {
            //Mage::helper('customreports')->log(sprintf("%s->setMinNocrosssellsCount:%d", __METHOD__, $_count));
            $session->setMinNocrosssellsCount($_count);
        }
        else {
            //$session->unsNocrosssellsCount();            
            // use previous or set default
            if (!$session->getMinNocrosssellsCount()) {
                $session->setMinNocrosssellsCount(FactoryX_CustomReports_Block_Nocrosssells::DEFAULT_MIN_CROSSSELLS);
            }
        }

        $this->loadLayout()
            ->_setActiveMenu('customreports/nocrosssells')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Custom No Crosssells Report'), Mage::helper('adminhtml')->__('Custom No Crosssells Report'))
            ->_addContent( $this->getLayout()->createBlock('customreports/nocrosssells') )
            ->renderLayout();
    }

    /**
     * Export no upsells report grid to CSV format
     */
    public function exportNocrosssellsCsvAction()
    {
        $fileName   = 'nocrosssells.csv';
        $content    = $this->getLayout()->createBlock('customreports/nocrosssells_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export no upsells report to Excel XML format
     */
    public function exportNocrosssellsExcelAction()
    {
        $fileName   = 'nocrosssellss.xml';
        $content    = $this->getLayout()->createBlock('customreports/nocrosssells_grid')
            ->getExcelFile($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }
}