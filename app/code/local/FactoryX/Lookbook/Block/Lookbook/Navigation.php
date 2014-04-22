<?php
/**
 *	That is the frontend block for the lookbook navigation links
 */
class FactoryX_Lookbook_Block_Lookbook_Navigation extends Mage_Core_Block_Template
{

	/**
	 *	Getter for the active lookbooks to include in the navigation menu
	 */
	public function getActiveLookbooks()
	{
		$activeLookbooks = Mage::getModel('lookbook/lookbook')->getCollection()->addStatusFilter(1)->addIncludeInNavFilter(1);
		
		return $activeLookbooks;
	}

}