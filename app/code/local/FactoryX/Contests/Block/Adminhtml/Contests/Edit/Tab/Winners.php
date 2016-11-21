<?php
/**
 * This block is slightly different as it displays a grid of the winners of the contest
 */
class FactoryX_Contests_Block_Adminhtml_Contests_Edit_Tab_Winners extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
	/**
	 * Constructor
	 */
	public function __construct()
    {
        parent::__construct();
        $this->setId('contest_winners');
        $this->setUseAjax(true);
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'contests/referrer_collection';
    }

	/**
     * Retrieve collection class
     *
     * @return FactoryX_Contests_Model_Mysql4_Referrer_Collection
     */
    protected function _prepareCollection()
    {
		// We filter the referrer by contest and by winners
        $collection = Mage::getResourceModel($this->_getCollectionClass())
            ->addFieldToSelect('*')
			->addContestFilter($this->getContestId())
            ->addWinnersFilter();
			
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return mixed
     */
    protected function _prepareColumns()
    {
        $this->addColumn('referrer_id', array(
            'header'    => Mage::helper('contests')->__('Referrer #'),
            'index'     => 'referrer_id',
            'width'     => '75px',
        ));

        $this->addColumn('email', array(
            'header' => Mage::helper('contests')->__('Email'),
            'index' => 'email',
        ));
		
		$this->addColumn('name', array(
            'header' => Mage::helper('contests')->__('Name'),
            'index' => 'name',
        ));
		
		$this->addColumn('mobile', array(
            'header'    => Mage::helper('contests')->__('Mobile'),
            'index'     => 'mobile'
        ));

		// We get the australian states (plus extra for new zealand and other)
        $ozStates = Mage::helper('contests')->getStates();
		
		$this->addColumn('state', array(
            'header' => Mage::helper('contests')->__('State'),
            'index' => 'state',
			'type' => 'options',
            'options' => $ozStates
        ));
		
		$this->addColumn('competition', array(
            'header'    => Mage::helper('contests')->__('Competition'),
            'index'     => 'competition',
			'width'		=> '400px'
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
	
	/**
     * Retrieve contest id
     *
     * @return int
     */
    public function getContestId()
    {
        return Mage::registry('contests_data')->getContestId();
    }
	
	/**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('contests')->__('Winner(s)');
    }

    /**
     * @return mixed
     */
    public function getTabTitle()
    {
        return Mage::helper('contests')->__('Contest Winner(s)');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}