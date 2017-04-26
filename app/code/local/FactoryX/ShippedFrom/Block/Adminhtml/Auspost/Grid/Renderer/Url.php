<?php

/** 
 * Class FactoryX_ShippedFrom_Block_Adminhtml_Auspost_Grid_Renderer_Url
 */
class FactoryX_ShippedFrom_Block_Adminhtml_Auspost_Grid_Renderer_Url
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
        $retVal = trim($retVal);
        $parts = parse_url(trim($retVal));
        if (array_key_exists('path', $parts) && !empty($parts['path'])) {
            $retVal = $parts['path'];
        } else if (!empty($retVal)) {
            $retVal = sprintf("invalid url: '%s'", $retVal);
        } else {
            $retVal = "";
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