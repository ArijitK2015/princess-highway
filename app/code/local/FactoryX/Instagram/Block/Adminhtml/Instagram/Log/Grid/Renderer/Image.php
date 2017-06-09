<?php

/**
 * Class FactoryX_Contests_Block_Adminhtml_Contests_Grid_Renderer_ListImageUrl
 */
class FactoryX_Instagram_Block_Adminhtml_Instagram_Log_Grid_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $imgUrl = $row->getImage();
        return "<img src=\"$imgUrl\" width=\"150\" height=\"150\"/>";
    }
}
