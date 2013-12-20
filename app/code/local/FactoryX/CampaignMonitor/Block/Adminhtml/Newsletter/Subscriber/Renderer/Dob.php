<?php
class FactoryX_CampaignMonitor_Block_Adminhtml_Newsletter_Subscriber_Renderer_Dob extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
	public function render(Varien_Object $row) 
	{
		$value =  $row->getData($this->getColumn()->getIndex());
		if ($value && $value != "0000-00-00") 
		{
			// Convert yyyy-mm-dd to mmm dd
			$value = date("jS M", strtotime($value));
		}
		else $value = "Not filled in";
		return $value;
	}
}