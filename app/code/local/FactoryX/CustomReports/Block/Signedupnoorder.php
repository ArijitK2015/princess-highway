<?php
class FactoryX_CustomReports_Block_Signedupnoorder extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('factoryx/customreports/signedupnoorder.phtml');
    }

    public function _beforeToHtml()
    {
        $this->setChild('grid', $this->getLayout()->createBlock('customreports/signedupnoorder_grid', 'customreports.grid'));
        return $this;
    }

}