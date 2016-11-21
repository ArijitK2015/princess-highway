<?php

/**
 * Class FactoryX_CreditmemoReasons_Block_Adminhtml_Report
 */
class FactoryX_CreditmemoReasons_Block_Adminhtml_Report extends Mage_Adminhtml_Block_Template
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('factoryx/creditmemoreasons/advancedgrid.phtml');
        // Set the right URL for the form which handles the dates
        $this->setFormAction(Mage::getUrl('*/*/index'));
    }

    /**
     * @return $this
     */
    public function _beforeToHtml()
    {
        $this->setChild('grid', $this->getLayout()->createBlock('creditmemoreasons/adminhtml_report_grid', 'reasonsreport.grid'));
        return $this;
    }

}