<?php
class FactoryX_CustomGrids_Block_Adminhtml_Report_Product_Sold_Renderer_Size extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
 
	public function render(Varien_Object $row) 
	{
		$value =  $row->getData($this->getColumn()->getIndex());
		
		$vals = preg_split("/-/", $value);
		
		if (strrpos($value, '-') && count($vals) > 1) 
		{
		    // get last index
            $value = $vals[count($vals) - 1];
            if (!preg_match("/([0-9]{1,})|(^[slm]$)|(sma|med|lar)/i", $value)) 
			{
                $value = '-';
            }
		}
		else 
		{
			$value = '-';
		}
		return sprintf("%s", $value);
	}
}
?>