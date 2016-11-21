<?php

/**
 * Class FactoryX_CreditmemoReasons_Block_Adminhtml_Report_Grid
 */
class FactoryX_CreditmemoReasons_Block_Adminhtml_Report_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
        $this->setId('reasonsReportGrid');
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareCollection()
    {
        // Get the session
        $session = Mage::getSingleton('core/session');

        // Dates for one week
        $store = Mage_Core_Model_App::ADMIN_STORE_ID;
        $timezone = Mage::app()->getStore($store)->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);
        date_default_timezone_set($timezone);

        // Automatic -30 days if no dates provided
        if ($session->getReasonreportsFrom())
        {
            $sDate = $session->getReasonreportsFrom();
            $sDate = str_replace('/', '-', $sDate);
            $sDate = strtotime($sDate);
            $sDate = date('Y-m-d H:i:s', $sDate);
        }
        else
        {
            $sDate = date('Y-m-d 00:00:00',
                Mage::getModel('core/date')->timestamp(strtotime('-30 days'))
            );
        }

        if ($session->getReasonreportsTo())
        {
            $eDate = $session->getReasonreportsTo();
            $eDate = str_replace('/', '-', $eDate);
            $eDate = strtotime($eDate);
            $eDate = date('Y-m-d H:i:s', $eDate);
        }
        else
        {
            $eDate = date('Y-m-d 23:59:59',
                Mage::getModel('core/date')->timestamp(time())
            );
        }

        // Get the credit memo created during the period provided
        $collection = Mage::getResourceModel('sales/order_creditmemo_collection')
            ->addAttributeToSelect('reason')
            ->addAttributeToFilter('created_at', array('from'=>$sDate, 'to'=>$eDate));

        $groupby = array('reason');

        if ($session->getReasonreportsReason())
        {
            //$collection->getSelect()->where(sprintf("reason = '%s'", $session->getReasonreportsReason()));
            $collection->addAttributeToFilter('reason', $session->getReasonreportsReason());
        }

        if ($session->getReasonreportsAddsku())
        {
            $collection->getSelect()->joinLeft(
                array('sfci' => Mage::getSingleton("core/resource")->getTableName('sales_flat_creditmemo_item')),
                'main_table.entity_id=sfci.parent_id AND sfci.row_total IS NOT NULL',
                array(
                    'sfci.sku as sku'
                )
            );
            $groupby[] = 'sku';
        }

        // Add a column for the quantity
        $collection->getSelect()->columns('COUNT(*) AS qty');

        // Group by
        $collection->getSelect()->group($groupby);

        Mage::helper('creditmemoreasons')->log($collection->getSelect()->__toString());

        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('reason', array(
            'header'    => Mage::helper('creditmemoreasons')->__('Reason'),
            'width'     => '50',
            'index'     => 'reason'
        ));

        if (Mage::getSingleton('core/session')->getReasonreportsAddsku())
        {
            $this->addColumn('sku', array(
                'header'    => Mage::helper('creditmemoreasons')->__('Sku'),
                'width'     => '150',
                'index'     => 'sku',
            ));

            $label = Mage::helper('creditmemoreasons')->__('Quantity of skus affected');
        }
        else
        {
            $label = Mage::helper('creditmemoreasons')->__('Quantity of credit memos affected');
        }

        $this->addColumn('qty', array(
            'header'    => $label,
            'width'     => '150',
            'index'     => 'qty',
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('creditmemoreasons')->__('CSV'));
        $this->addExportType('*/*/exportPdf', Mage::helper('creditmemoreasons')->__('PDF'));

        return parent::_prepareColumns();
    }

    /**
     * getPdfFile
     */
    public function getPdfFile() {
        $this->_isExport = true;
        $this->_prepareGrid();
        $this->getCollection()->getSelect()->limit();
        $this->getCollection()->setPageSize(0);
        $this->getCollection()->load();
        $this->_afterLoadCollection();

        //$pdf = $this->_pdfGenerate();
        //return $pdf->render();
        
        $pdfReport = Mage::getModel('creditmemoreasons/Report_Pdf');
        $pdf = $pdfReport->generate($this->_columns, $this->_collection);
        return $pdf->render();
    }

}