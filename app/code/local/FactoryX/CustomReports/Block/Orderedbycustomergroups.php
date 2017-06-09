<?php

/**
 * Class FactoryX_CustomReports_Block_Orderedbycustomergroups
 */
class FactoryX_CustomReports_Block_Orderedbycustomergroups extends FactoryX_CustomReports_Block_Customreport
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('factoryx/customreports/advancedgrid.phtml');
        $this->setTitle('Products Ordered By Customer Groups');
        // Set the right URL for the form which handles the dates
        $this->setFormAction(Mage::getUrl('*/*/index'));
    }

    /**
     * @return $this
     */
    public function _beforeToHtml()
    {
        $this->setChild('grid', $this->getLayout()->createBlock('customreports/orderedbycustomergroups_grid', 'customreports.grid'));
        return $this;
    }

}