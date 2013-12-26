<?php
// Mage/Adminhtml/Block/Catalog/Category/Tab/Renderer/Thumbnail.php

class Mage_Adminhtml_Block_Catalog_Category_Tab_Renderer_Thumbnail extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	
    protected $_values;

    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row) 
	{
		if ($row->getImage() == 'no_selection' || $row->getImage() == '') {
			$imgUrl = Mage::getDesign()->getSkinUrl('images/catalog/product/placeholder/image.jpg');
		}
		else {
			$imgUrl = Mage::getBaseUrl('media').'catalog/product'. $row->getImage();
		}
		return "<img src=\"$imgUrl\" width=\"75\" height=\"75\"/>";
    }
}
