<?php
class Mage_Adminhtml_Block_Sales_Order_Grid_Renderer_Name extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	
	public function render(Varien_Object $row) {
		$name = $row->getData($this->getColumn()->getIndex());
		$email = $row->getData('customer_email');
		return '<a href="mailto:'.$email.'">'.$name.'</a>';
	}
}

?>