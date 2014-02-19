<?php
class FactoryX_CustomReports_Block_Worstsellers extends FactoryX_CustomReports_Block_Customreport
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('factoryx/customreports/advancedgrid.phtml');
		$this->setTitle('Custom Worstsellers Report');
		// Set the right URL for the form which handles the dates
		$this->setFormAction(Mage::getUrl('*/*/index'));
    }

    public function _beforeToHtml()
    {
        $this->setChild('grid', $this->getLayout()->createBlock('customreports/worstsellers_grid', 'customreports.grid'));
        return $this;
    }

}