<?php

class FactoryX_Contests_Block_Adminhtml_Referees_Grid extends Mage_Adminhtml_Block_Widget_Grid 
{

    public function __construct() 
	{
        parent::__construct();
        $this->setId('refereesGrid');
        $this->setDefaultSort('referee_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _getStore() 
	{
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection() 
	{
        $collection = Mage::getModel('contests/referee')
						->getCollection()
						->addReferrerData()
						->addContestData();
						
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() 
	{
        $this->addColumn('referee_id', array(
            'header' => Mage::helper('contests')->__('Referee #'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'referee_id',
        ));

        $this->addColumn('email', array(
            'header' => Mage::helper('contests')->__('Referee Email'),
            'align' => 'left',
            'index' => 'email',
        ));
		
		$this->addColumn('contest_title', array(
            'header' => Mage::helper('contests')->__('Contest Title'),
            'width' => '50px',
            'index' => 'contest_title',
			'filter_index'	=>	'contest.title',
        ));

        $this->addColumn('referrer_email', array(
            'header' => Mage::helper('contests')->__('Referrer Email'),
            'align' => 'left',
            'index' => 'referrer_email',
			'filter_index'	=>	'referrer.email',
        ));
		
		$this->addColumn('referrer_id', array(
            'header' => Mage::helper('contests')->__('Referrer #'),
            'align' => 'left',
            'index' => 'referrer_id',
        ));

        $this->addColumn('entry_date', array(
            'header' => Mage::helper('contests')->__('Entry Date'),
            'index' => 'entry_date',
            'type' => 'datetime',
            'width' => '120px',
            'gmtoffset' => true,
            'default' => ' -- '
        ));

        return parent::_prepareColumns();
    }
}
