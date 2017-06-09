<?php

/**
 * Class FactoryX_ModulesFeed_Block_Adminhtml_Template_Grid_Renderer_FolderPathExists
 */
class FactoryX_ModulesFeed_Block_Adminhtml_Template_Grid_Renderer_FolderPathExists extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $pathExists = $row->getFolderPathExists();
        if ($pathExists == "false")
        {
            return "<p class='error'>".Mage::helper('modulesfeed')->__('%s does not exist',$row->getFolderPath())."</p>";
        }
        else return "<p class='pass'>".$pathExists."</p>";
    }
}
