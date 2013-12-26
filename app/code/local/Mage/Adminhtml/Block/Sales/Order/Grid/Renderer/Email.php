<?php
class Mage_Adminhtml_Block_Sales_Order_Grid_Renderer_Email extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	
	public function render(Varien_Object $row) {
		$email = $row->getData($this->getColumn()->getIndex());
		return '<a href="mailto:"'.$email.'"">'.$email.'</a>';
	}
}

?>