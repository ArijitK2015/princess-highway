<?php

/**
 * Class FactoryX_ModulesFeed_Block_Adminhtml_Template_Grid_Renderer_DataVersion
 */
class FactoryX_ModulesFeed_Block_Adminhtml_Template_Grid_Renderer_DataVersion extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $dataVersion = $row->getDataVersion();
        if ($dataVersion != $row->getVersion() && $row->getDataEntry())
        {
            return "<p class='error'>".$dataVersion."</p>";
        }
        elseif (!$dataVersion)
        {
            return "<p class='notapplicable'>".$dataVersion."</p>";
        }
        else return $dataVersion;
    }
}
