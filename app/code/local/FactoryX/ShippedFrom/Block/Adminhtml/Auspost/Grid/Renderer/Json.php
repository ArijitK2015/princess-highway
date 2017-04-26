<?php

/** 
 * Class FactoryX_ShippedFrom_Block_Adminhtml_Auspost_Grid_Renderer_Json
 */
class FactoryX_ShippedFrom_Block_Adminhtml_Auspost_Grid_Renderer_Json
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Options
{
    /**
     * Renderer for the action column
     * @param Varien_Object $row
     * @return string
     */    
    public function render(Varien_Object $row)
    {
        $retVal = $row->getData($this->getColumn()->getIndex());
        if ($retVal != "") {
             $retVal = substr($retVal, 0, 10) . "...";
        }

        return $retVal;
    }

    /**
     *  Render for export
     * @param Varien_Object $row
     * @return string
     */
    public function renderExport(Varien_Object $row)
    {
        return $this->render($row);
    }
}