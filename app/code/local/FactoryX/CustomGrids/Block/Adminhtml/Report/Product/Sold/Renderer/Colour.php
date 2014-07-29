<?php
class FactoryX_CustomGrids_Block_Adminhtml_Report_Product_Sold_Renderer_Colour extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
 
	public function render(Varien_Object $row) 
	{
		$value =  $row->getData($this->getColumn()->getIndex());
		
		$vals = preg_split("/-/", $value);
		
		if (strrpos($value, '-') && count($vals) > 1) 
		{
		    // get last index
            $value = $vals[1];
		}
		else 
		{
			$value = 'na';
		}
		return sprintf("%s", $value);
	}
}
?>