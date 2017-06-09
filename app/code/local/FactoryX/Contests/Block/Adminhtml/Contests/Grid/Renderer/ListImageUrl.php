<?php

/**
 * Class FactoryX_Contests_Block_Adminhtml_Contests_Grid_Renderer_ListImageUrl
 */
class FactoryX_Contests_Block_Adminhtml_Contests_Grid_Renderer_ListImageUrl extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row) 
	{
		if ($row->getListImageUrl() == 'no_selection' || $row->getListImageUrl() == '' || $row->getListImageUrl() == NULL) 
		{
			return '';
		}
		else 
		{
			$imgUrl = Mage::getBaseUrl('media') . 'contest' . $row->getListImageUrl();
			return "<img src=\"$imgUrl\" width=\"94\" height=\"75\"/>";
		}
    }
}
