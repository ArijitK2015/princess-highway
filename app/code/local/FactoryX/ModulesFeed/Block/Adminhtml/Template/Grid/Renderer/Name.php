<?php

/**
 * Class FactoryX_ModulesFeed_Block_Adminhtml_Template_Grid_Renderer_Name
 */
class FactoryX_ModulesFeed_Block_Adminhtml_Template_Grid_Renderer_Name extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $name = $row->getName();
        if (preg_match("/FactoryX/",$name))
        {
            $url = "https://bitbucket.org/factoryx-developers/".strtolower($name);
            return "<a target='_blank' rel='noopener noreferrer' href='$url'>".$name."</a>";
        }
        else return $name;
    }
}
