<?php

/**
 * Class FactoryX_CustomReports_Block_Wishlist
 */
class FactoryX_CustomReports_Block_Wishlist extends FactoryX_CustomReports_Block_Customreport
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('factoryx/customreports/advancedgrid.phtml');
        $this->setTitle('Wishlist Report');
        // Set the right URL for the form which handles the dates
        $this->setFormAction(Mage::getUrl('*/*/index'));
    }

    /**
     * @return $this
     */
    public function _beforeToHtml()
    {
        $this->setChild('grid', $this->getLayout()->createBlock('customreports/wishlist_grid', 'customreports.grid'));
        return $this;
    }

}