<?php

/**
 * Class FactoryX_AWAdvancedreportsMod_Helper_Setup
 */
class FactoryX_AWAdvancedreportsMod_Helper_Setup extends AW_Advancedreports_Helper_Setup
{
    public function getGrid()
    {
        if(!$this->getData('current_grid')
            && $this->getReportRoute() == FactoryX_AWAdvancedreportsMod_Helper_Data::ROUTE_ADVANCED_REGION) {
            $grid = Mage::app()->getLayout()->createBlock('awadvancedreportsmod/' . $this->getReportRoute() . '_grid');
            $this->setData('current_grid', $grid);
        }
        return parent::getGrid();
    }
}
