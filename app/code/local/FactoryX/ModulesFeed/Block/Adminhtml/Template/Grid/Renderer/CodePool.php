<?php

/**
 * Class FactoryX_ModulesFeed_Block_Adminhtml_Template_Grid_Renderer_CodePool
 */
class FactoryX_ModulesFeed_Block_Adminhtml_Template_Grid_Renderer_CodePool extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $codePool = $row->getCodePool();
        if ($codePool == "undefined")
        {
            return Mage::helper('modulesfeed')->__("This module is defined within xml files under the app/etc/modules folder but is not configured properly. They may be defined under not properly named file or different module name files.");
        }
        else if (!$codePool)
        {
            return "<p class='error'>".Mage::helper('modulesfeed')->__("Undefined")."</p>";
        }
        else return $codePool;
    }
}
