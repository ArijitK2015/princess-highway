<?php
class Mage_Adminhtml_Block_Report_Product_Sold_Renderer_Size extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
 
	public function render(Varien_Object $row) {
		$value =  $row->getData($this->getColumn()->getIndex());
		
		if (strrpos($value, '-')) {
			$value = substr($value, strrpos($value, '-') + 1);
		}
		else {
			$value = 'na';
		}
		return sprintf("%s", $value);
	}
}
?>