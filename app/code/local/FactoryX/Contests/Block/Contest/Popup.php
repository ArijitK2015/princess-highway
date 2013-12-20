<?php

class FactoryX_Contests_Block_Contest_Popup extends Mage_Core_Block_Template
{

	protected $popupText = null;
	protected $popupIdentifier = null;
	protected $popupContestId = null;
	protected $popupContestReferers = null;

	public function hasPopupContest($store = null)
	{
		try
		{
			if ($store == "")	$store = null;
			
			$popupContests = Mage::getResourceModel('contests/contest_collection')
									->addIsPopupFilter(1)
									->addDisplayedFilter(1)
									->addStoreFilter($store);
								
			if (count($popupContests) == 1)
			{
				foreach($popupContests as $popupContest)
				{
					$this->popupText = $popupContest->getPopupText();
					$this->popupIdentifier = $popupContest->getIdentifier();
					$this->popupContestId = $popupContest->getId();
					$this->popupContestReferers = $popupContest->getPopupReferers();
				}
				
				return true;
			}
			elseif(count($popupContests) > 1)
			{
				throw new Exception("It seems like there are more than one popup contest");
			}
			else
			{
				return false;
			}
		}
		catch (Exception $e)
		{
			Mage::helper('contests')->log("Exception caught in %s under % with message: %s", __FILE__, __FUNCTION__, $e->getMessage());
			Mage::getSingleton('customer/session')->addError($this->__('There was a problem loading the popup contest'));
			return false;
		}
	}
	
	public function getReferersLimitation()
	{
		if ($this->popupContestReferers != "")
		{
			$limitationsArray = explode(',',$this->popupContestReferers);
			return $limitationsArray;
		}
		else return false;
	}	
	
	public function getPopupText()
	{
		return $this->popupText;
	}	
	
	public function getPopupIdentifier()
	{
		return $this->popupIdentifier;
	}
	
	public function getPopupContestId()
	{
		return $this->popupContestId;
	}
	
}
