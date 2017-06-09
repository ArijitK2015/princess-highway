<?php

/**
 * Class FactoryX_CustomReports_Block_Noupsells
 */
class FactoryX_CustomReports_Block_Noupsells extends FactoryX_CustomReports_Block_Customreport
{
    
    const DEFAULT_MIN_UPSELLS = 3;
    
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        //$this->setTemplate('factoryx/customreports/grid.phtml');
        $this->setTemplate('factoryx/customreports/noupsells.phtml');
		$this->setTitle('Custom No Upsells Report');
		$this->setSideNote('Note. the grid displays only simple and configurable products with visibility set to "Catalog, Search".');
    }

    /**
     * @return $this
     */
    public function _beforeToHtml()
    {
        $this->setChild('grid', $this->getLayout()->createBlock('customreports/noupsells_grid', 'customreports.grid'));
        return $this;
    }

}