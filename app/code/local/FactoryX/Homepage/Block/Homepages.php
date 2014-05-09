<?php

class FactoryX_Homepage_Block_Homepages extends Mage_Core_Block_Template 
{
	
	/**
	 *	Retrieve the current homepage for the frontend
	 */
	public function getCurrentHomepages($store = null)     
    {
		if ($store == "")	$store = null;
		
		try
		{
			// If we are on the preview page
			if (Mage::app()->getRequest()->getActionName() == "preview")
			{
				// We retrieve the home page based on the id
				$currentHomepages = Mage::getModel('homepage/homepage')->load(Mage::app()->getRequest()->getParam('id'));
			}
			else
			{
				// Else we retrieve the enabled homepages
				$currentHomepages = Mage::getModel('homepage/homepage')
									->getCollection()
									->addDisplayedFilter(1)
									->addStoreFilter($store)
									->addAttributeToSort('sort_order');
			}
			
			// Ensure the homepage is viewable in the store
			if (!Mage::app()->isSingleStoreMode()) 
			{
				foreach ($currentHomepages as $currentHomepage)
				{
					if ($currentHomepage->isStoreViewable()) 
						continue;
					else 
						throw new Exception ('This homepage is not available with this store.');
				}
				return $currentHomepages;
			}
			else
			{
				return $currentHomepages;
			}
		}
		catch (Exception $e)
		{
			Mage::helper('homepage')->log($this->__('Exception caught in %s under % with message: %s', __FILE__, __FUNCTION__, $e->getMessage()));
			return false;
		}
    }
	
	/**
	 * Build the frontend home page
	 */
	public function __construct()
    {
        parent::__construct();

		// Retrieve the current home page and use the store id if multistore
		if (!Mage::app()->isSingleStoreMode())
		{
			$storeId = Mage::app()->getStore()->getId();
		}
		else
		{
			$storeId = "";
		}
		
		$homepages = $this->getCurrentHomepages($storeId);
		
		$isModuleEnabled = Mage::helper('homepage')->isHomepageModuleUsed();
		
		Mage::log($isModuleEnabled);
		
		if ($homepages && $isModuleEnabled)
		{
			// Set the template
			$this->setTemplate('factoryx/homepage/homepages.phtml');
			// Assign the homepages
			$this->setHomepages($homepages);
		}
    }
	
	public function _beforeToHtml()
	{
		// Retrieve the current home page and use the store id if multistore
		if (!Mage::app()->isSingleStoreMode())
		{
			$storeId = Mage::app()->getStore()->getId();
		}
		else
		{
			$storeId = "";
		}
		
		$homepages = $this->getCurrentHomepages($storeId);
		
		$isModuleEnabled = Mage::helper('homepage')->isHomepageModuleUsed();
		
		Mage::log($isModuleEnabled);
		
		if ($homepages && $isModuleEnabled)
		{
			$count = 0;
			foreach($homepages as $homepage)
			{
				$this->setChild('homepage_'.++$count, $this->getLayout()->createBlock('homepage/homepage', 'homepage', array('homepage'=>$homepage)));
			}
		}
	}
}
?>