<?php

class FactoryX_Lookbook_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
    {		
		 $this->_forward('view');
    }
	
	public function viewAction()
    {		
		$this->loadLayout();
		
		try
		{
			// Load the lookbook
			$lookbookId = $this->getRequest()->getParam('id');
			$lookbook = Mage::getModel('lookbook/lookbook')->load($lookbookId);
			
			// Ensure the lookbook is viewable in the store
			if (!Mage::app()->isSingleStoreMode()) 
			{
				if (!$lookbook->isStoreViewable()) 
					throw new Exception ('This lookbook is not available with this store');
			}
			
			// Initiate the session messages
			$this->_initLayoutMessages('core/session');
			
			// In order to get the title so we can set the head title
			$lookbookTitle = $lookbook->getTitle();
			$this->getLayout()->getBlock('head')->setTitle($lookbookTitle);
		}
		catch (Exception $e)
		{
			Mage::helper('lookbook')->log($this->__('Exception caught in %s under % with message: %s', __FILE__, __FUNCTION__, $e->getMessage()));
			Mage::getSingleton('core/session')->addException($e, $this->__('There was a problem loading the lookbook'));
			$this->_redirectReferer();
			return;
		}
		
		$this->renderLayout();

    }
	
	public function slideshowAction()
    {		
		$this->loadLayout();
		
		try
		{
			// Load the lookbook
			$lookbookId = $this->getRequest()->getParam('id');
			$lookbook = Mage::getModel('lookbook/lookbook')->load($lookbookId);
			
			// Ensure the lookbook is viewable in the store
			if (!Mage::app()->isSingleStoreMode()) 
			{
				if (!$lookbook->isStoreViewable()) 
					throw new Exception ('This lookbook is not available with this store');
			}
			
			// Initiate the session messages
			$this->_initLayoutMessages('core/session');
			
			// In order to get the title so we can set the head title
			$lookbookTitle = $lookbook->getTitle();
			$this->getLayout()->getBlock('head')->setTitle($lookbookTitle);
		}
		catch (Exception $e)
		{
			Mage::helper('lookbook')->log($this->__('Exception caught in %s under % with message: %s', __FILE__, __FUNCTION__, $e->getMessage()));
			Mage::getSingleton('core/session')->addException($e, $this->__('There was a problem loading the lookbook'));
			$this->_redirectReferer();
			return;
		}
		
		$this->renderLayout();

    }
	
}