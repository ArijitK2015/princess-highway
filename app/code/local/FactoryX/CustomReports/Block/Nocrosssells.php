<?php

/**
 * Class FactoryX_CustomReports_Block_Nocrosssells
 */
class FactoryX_CustomReports_Block_Nocrosssells extends FactoryX_CustomReports_Block_Customreport
{

    const DEFAULT_MIN_CROSSSELLS = 3;

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        //$this->setTemplate('factoryx/customreports/grid.phtml');
        $this->setTemplate('factoryx/customreports/nocrosssells.phtml');
        $this->setTitle('Custom No Crosssells Report');
        $this->setSideNote('Note. the grid displays only configurable products. You can use the visibility and status filter to only get the products which are available on the store.');
    }

    /**
     * @return $this
     */
    public function _beforeToHtml()
    {
        $this->setChild('grid', $this->getLayout()->createBlock('customreports/nocrosssells_grid', 'customreports.grid'));
        return $this;
    }

}