<?php
class Mage_Adminhtml_Block_Catalog_Product_Renderer_Brand extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	
	public function render(Varien_Object $row) {
		$value =  $row->getData($this->getColumn()->getIndex());
		Mage::log(__METHOD__ . "=" . $value);
		if ($value) {
			$value = intval($value);
		}
		//return '<span style="color:red;">'.$value.'</span>';
		return $value;
	}
}

?>