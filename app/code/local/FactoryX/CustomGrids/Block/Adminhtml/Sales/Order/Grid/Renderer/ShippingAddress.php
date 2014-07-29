<?php
class FactoryX_CustomGrids_Block_Adminhtml_Sales_Order_Grid_Renderer_ShippingAddress extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	
	public function render(Varien_Object $row) 
	{
		$address = $row->getData($this->getColumn()->getIndex());
		return preg_replace('/\n+/', ' ', $address);
	}
}

?>