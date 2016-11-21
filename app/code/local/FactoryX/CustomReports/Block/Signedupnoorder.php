<?php

/**
 * Class FactoryX_CustomReports_Block_Signedupnoorder
 */
class FactoryX_CustomReports_Block_Signedupnoorder extends FactoryX_CustomReports_Block_Customreport
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('factoryx/customreports/grid.phtml');
        $this->setTitle('Custom Signed Up But Never Shopped Report');
    }

    /**
     * @return $this
     */
    public function _beforeToHtml()
    {
        $this->setChild('grid', $this->getLayout()->createBlock('customreports/signedupnoorder_grid', 'customreports.grid'));
        return $this;
    }

}