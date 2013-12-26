<?php
class Mage_Adminhtml_Block_Catalog_Product_Renderer_Gender extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	
	public function render(Varien_Object $row) {
		$value = $row->getData($this->getColumn()->getIndex());		
		if (preg_match('/^[a-zA-Z]{1}[g|G]/', $value)) {
			$value = "Girls";
		}
		else if (preg_match('/^[a-zA-Z]{1}[b|B]/', $value)) {
			$value = "Boys";
		}
		else {
			$value = "Nonspecific";
		}
		return $value;
	}
}

?>