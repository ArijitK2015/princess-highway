<?php
class FactoryX_CustomReports_Block_Noupsells extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('factoryx/customreports/noupsells.phtml');
    }

    public function _beforeToHtml()
    {
        $this->setChild('grid', $this->getLayout()->createBlock('customreports/noupsells_grid', 'customreports.grid'));
        return $this;
    }

}