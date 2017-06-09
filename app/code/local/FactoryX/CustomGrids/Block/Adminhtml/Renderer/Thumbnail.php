<?php

/**
 * Class FactoryX_CustomGrids_Block_Adminhtml_Renderer_Thumbnail
 */
class FactoryX_CustomGrids_Block_Adminhtml_Renderer_Thumbnail extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	
    protected $_values;

    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row) 
	{
		if ($row->getThumbnail() == 'no_selection' || $row->getThumbnail() == '')
		{
			$imgUrl = Mage::getDesign()->getSkinUrl('images/catalog/product/placeholder/image.jpg');
		}
		else 
		{
			$imgUrl = Mage::getBaseUrl('media').'catalog/product'. $row->getThumbnail();
		}
		return "<img src=\"$imgUrl\" width=\"75\" height=\"75\"/>";
    }
}
