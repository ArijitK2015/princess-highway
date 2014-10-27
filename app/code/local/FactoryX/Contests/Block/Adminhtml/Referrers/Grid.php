<?php

class FactoryX_Contests_Block_Adminhtml_Referrers_Grid extends Mage_Adminhtml_Block_Widget_Grid 
{

    public function __construct() 
	{
        parent::__construct();
        $this->setId('referrersGrid');
        $this->setDefaultSort('referrer_id');
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
        $collection = Mage::getModel('contests/referrer')->getCollection()->addContestData();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() 
	{
        $this->addColumn('referrer_id', array(
            'header' => Mage::helper('contests')->__('Referrer #'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'referrer_id',
        ));
		
		$this->addColumn('contest_title', array(
            'header' => Mage::helper('contests')->__('Contest Title'),
            'width' => '50px',
            'index' => 'contest_title',
			'filter_index'	=>	'contest.title',
        ));
		
		$this->addColumn('contest_id', array(
            'header' => Mage::helper('contests')->__('Contest #'),
            'width' => '50px',
            'index' => 'contest_id',
			'filter_index'	=>	'contest.contest_id',
        ));

        $this->addColumn('email', array(
            'header' => Mage::helper('contests')->__('Email'),
            'align' => 'left',
            'index' => 'email',
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('contests')->__('Name'),
            'align' => 'left',
            'index' => 'name',
        ));
		
		$this->addColumn('mobile', array(
            'header' => Mage::helper('contests')->__('Mobile'),
            'align' => 'left',
            'index' => 'mobile',
        ));
		
		$ozStates = Mage::helper('contests')->getStates();
		
		$this->addColumn('state', array(
            'header' => Mage::helper('contests')->__('State'),
            'align' => 'left',
            'index' => 'state',
			'type' => 'options',
            'options' => $ozStates
        ));
		
		$this->addColumn('competition', array(
            'header' => Mage::helper('contests')->__('Competition'),
            'align' => 'left',
            'index' => 'competition',
        ));

        $this->addColumn('entry_date', array(
            'header' => Mage::helper('contests')->__('Entry Date'),
            'index' => 'entry_date',
            'type' => 'datetime',
            'width' => '120px',
            'gmtoffset' => true,
            'default' => ' -- '
        ));

        $this->addColumn('is_winner', array(
            'header' => Mage::helper('contests')->__('Winner'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'is_winner',
            'type' => 'options',
            'options' => array(
                0 => Mage::helper('contests')->__('No'),
                1 => Mage::helper('contests')->__('Yes'),
            ),
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('contests')->__('CSV'));
		$this->addExportType('*/*/exportExcel', Mage::helper('contests')->__('Excel'));
        return parent::_prepareColumns();
    }
}
