<?php

/**
 * Class FactoryX_ModulesFeed_Block_Adminhtml_Template_Grid_Renderer_DataEntry
 */
class FactoryX_ModulesFeed_Block_Adminhtml_Template_Grid_Renderer_DataEntry extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $dataEntry = $row->getDataEntry();
        if (!$dataEntry)
        {
            return "<p class='notapplicable'>".$dataEntry."</p>";
        }
        else return $dataEntry;
    }
}
