<?php

/**
 * Class FactoryX_Contests_Block_Contest_Terms
 */
class FactoryX_Contests_Block_Contest_Terms extends Mage_Core_Block_Template
{

	/**
	 *	Get the current contest title
	 */
	public function getCurrentContestTitle()     
    {
		try
		{
			if($this->getRequest()->getParam('id'))
			{
				$title = Mage::getModel('contests/contest')->load($this->getRequest()->getParam('id'))->getTitle();	
			}
			else $title = '';
			
			return $title;
		}
		catch (Exception $e)
		{
			Mage::helper('contests')->log("Exception caught in %s under % with message: %s", __FILE__, __FUNCTION__, $e->getMessage());
			Mage::getSingleton('customer/session')->addError($this->__('There was a problem loading the terms and conditions'));
			$this->_redirectReferer();
			return;
		}
    }
	
	/**
	 * Get the current contest terms and conditions
	 */
	public function getCurrentContestTerms()     
    {
		try
		{
			if($this->getRequest()->getParam('id'))
			{
				$terms = Mage::getModel('contests/contest')->load($this->getRequest()->getParam('id'))->getTerms();
				return $terms;
			}
			else return '';
		}
		catch (Exception $e)
		{
			Mage::helper('contests')->log("Exception caught in %s under % with message: %s", __FILE__, __FUNCTION__, $e->getMessage());
			Mage::getSingleton('customer/session')->addError($this->__('There was a problem loading the terms and conditions'));
			$this->_redirectReferer();
			return;
		}
    }
	
}
