<?php

/**
 * Class FactoryX_CustomReports_NoupsellsController
 */
class FactoryX_CustomReports_Adminhtml_NoupsellsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/products/noupsells');
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
            $session->setNoupsellsSort($this->getRequest()->getParam('sort'));
        }
        else {
            //$session->unsNoupsellsSort();
            $session->setNoupsellsSort("name");
        }
        if ($this->getRequest()->getParam('dir')) {
            $session->setNoupsellsDir($this->getRequest()->getParam('dir'));
        }
        else {
            //$session->unsNoupsellsSort();
            $session->setNoupsellsDir("ASC");
        }
        
        // get params
        $_count = $this->getRequest()->getParam('count');
        //Mage::helper('customreports')->log(sprintf("%s->_count:%s|%s", __METHOD__, $_count, intval($_count)));
        
        // Use the session to manage the count
        if (strlen($_count) && is_numeric(intval($_count))) {
            //Mage::helper('customreports')->log(sprintf("%s->setMinNoupsellsCount:%d", __METHOD__, $_count));
            $session->setMinNoupsellsCount($_count);
        }
        else {
            //$session->unsNoupsellsCount();            
            // use previous or set default
            if (!$session->getMinNoupsellsCount()) {
                $session->setMinNoupsellsCount(FactoryX_CustomReports_Block_Noupsells::DEFAULT_MIN_UPSELLS);
            }
        }
        
        $this->loadLayout()
             ->_setActiveMenu('customreports/noupsells')
             ->_addBreadcrumb(Mage::helper('adminhtml')->__('Custom No Upsells Report'), Mage::helper('adminhtml')->__('Custom No Upsells Report'))
            ->_addContent( $this->getLayout()->createBlock('customreports/noupsells') )
            ->renderLayout();
    }

    /**
     * Export no upsells report grid to CSV format
     */
    public function exportNoupsellsCsvAction()
    {
        $fileName   = 'noupsells.csv';
        $content    = $this->getLayout()->createBlock('customreports/noupsells_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export no upsells report to Excel XML format
     */
    public function exportNoupsellsExcelAction()
    {
        $fileName   = 'noupsellss.xml';
        $content    = $this->getLayout()->createBlock('customreports/noupsells_grid')
            ->getExcelFile($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }
}