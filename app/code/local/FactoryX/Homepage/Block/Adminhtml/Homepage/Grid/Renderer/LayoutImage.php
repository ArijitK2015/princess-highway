<?php
class FactoryX_Homepage_Block_Adminhtml_Homepage_Grid_Renderer_LayoutImage extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row) 
	{
		// If the layout of the row is not set we display nothing
		if (!$row->getLayout()) 
		{
			return '';
		}
		else 
		{
			// We generate the URL of the chosen layout
			$skinUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml'.DS.'default'.DS.'default';
			// And display it in the grid
			return "<img src='" . $skinUrl . DS . "images" . DS . "factoryx" . DS . "homepage" . DS . $row->getLayout() . ".png' width='auto' height='75' />";
		}
    }
}
