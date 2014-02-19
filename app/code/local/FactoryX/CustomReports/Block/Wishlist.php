<?php
class FactoryX_CustomReports_Block_Wishlist extends FactoryX_CustomReports_Block_Customreport
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('factoryx/customreports/grid.phtml');
		$this->setTitle('Wishlist Report');
    }

    public function _beforeToHtml()
    {
        $this->setChild('grid', $this->getLayout()->createBlock('customreports/wishlist_grid', 'customreports.grid'));
        return $this;
    }

}