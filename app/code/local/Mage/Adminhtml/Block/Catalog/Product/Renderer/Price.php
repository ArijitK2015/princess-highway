<?php
class Mage_Adminhtml_Block_Catalog_Product_Renderer_Price extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	public function render(Varien_Object $row) {
		$value =  $row->getData($this->getColumn()->getIndex());
		if ($value) {
			$value = sprintf("$%01.2f", $value);
		}
		// money_format('%n'
		return '<span style="text-align:right;">' . $value . '</span>';
	}
}
?>