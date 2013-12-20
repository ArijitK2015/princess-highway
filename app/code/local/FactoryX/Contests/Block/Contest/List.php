<?php

class FactoryX_Contests_Block_Contest_List extends Mage_Core_Block_Template
{
	protected function _construct()
    {
        $this->addData(array(
            'cache_lifetime'    => 86400,
            'cache_tags'        => array(FactoryX_Contests_Model_Contest::CACHE_TAG),
            'cache_key'         => 'contests_list',
        ));
		
        $collection = $this->getCurrentContests();
        $this->setCollection($collection);
    }
	
	protected function _prepareLayout()
    {
        parent::_prepareLayout();
 
        $pager = $this->getLayout()->createBlock('page/html_pager', 'contests.pager');
        $pager->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();
        return $this;
    }
 
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
	
	/**
	 *	Retrieve the current contests for the frontend
	 */
	public function getCurrentContests()     
    {
		try
		{
			$currentContests = Mage::getResourceModel('contests/contest_collection')
									->addDisplayedFilter(1)
									->addInListFilter(1)
									->addStoreFilter();
			
			return $currentContests;
		}
		catch (Exception $e)
		{
			Mage::helper('contests')->log("Exception caught in %s under % with message: %s", __FILE__, __FUNCTION__, $e->getMessage());
			Mage::getSingleton('customer/session')->addError($this->__('There was a problem loading the contests'));
			$this->_redirectReferer();
			return;
		}
    }
   
}