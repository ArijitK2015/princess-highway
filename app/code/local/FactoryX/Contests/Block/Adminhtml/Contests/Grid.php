<?php

class FactoryX_Contests_Block_Adminhtml_Contests_Grid extends Mage_Adminhtml_Block_Widget_Grid 
{

    public function __construct() 
	{
        parent::__construct();
        $this->setId('contestsGrid');
        $this->setDefaultSort('contest_id');
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
        $collection = Mage::getModel('contests/contest')->getCollection()
							->addFieldToSelect(array('contest_id','title','identifier','status','start_date','end_date','type','added','list_image_url','is_in_list','is_popup','new_subscriber_counter'));
        $store = $this->_getStore();
        if ($store->getId()) 
		{
            $collection->addStoreFilter($store);
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() 
	{
        $this->addColumn('contest_id', array(
            'header' => Mage::helper('contests')->__('Contest #'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'contest_id',
        ));
		
		$this->addColumn('list_image_url', array(
            'header' => Mage::helper('contests')->__('List Image'),
            'align' => 'left',
			'width'	=>	'94px',
			'renderer'  => 'FactoryX_Contests_Block_Adminhtml_Contests_Grid_Renderer_ListImageUrl',
			'filter'	=> false,
			'sortable'	=> false,
            'index' => 'list_image_url',
        ));

        $this->addColumn('title', array(
            'header' => Mage::helper('contests')->__('Title'),
            'align' => 'left',
            'index' => 'title',
        ));

        $this->addColumn('identifier', array(
            'header' => Mage::helper('contests')->__('Identifier'),
            'align' => 'left',
            'index' => 'identifier',
        ));
		
		$statuses = Mage::getSingleton('contests/status')->getOptionArray();

        $this->addColumn('status', array(
            'header' => Mage::helper('contests')->__('Status'),
            'width' => '150px',
            'index' => 'status',
			'type' => 'options',
            'options' => $statuses,
        ));


		// Output format for the start and end dates
		$outputFormat = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
		
        $this->addColumn('start_date', array(
            'header' => Mage::helper('contests')->__('Start Date'),
            'index' => 'start_date',
            'type' => 'datetime',
            'width' => '120px',
			'format' => $outputFormat,
            'default' => ' -- '
        ));

        $this->addColumn('end_date', array(
            'header' => Mage::helper('contests')->__('End Date'),
            'index' => 'end_date',
            'width' => '120px',
            'type' => 'datetime',
			'format' => $outputFormat,
            'default' => ' -- '
        ));

        $this->addColumn('type', array(
            'header' => Mage::helper('contests')->__('Type'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'type',
            'type' => 'options',
            'options' => array(
                1 => Mage::helper('contests')->__('Refer A Friend'),
                2 => Mage::helper('contests')->__('Give Away')
            ),
        ));
		
		$this->addColumn('is_in_list', array(
            'header' => Mage::helper('contests')->__('Displayed In List'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'is_in_list',
            'type' => 'options',
            'options' => array(
                0 => Mage::helper('contests')->__('No'),
                1 => Mage::helper('contests')->__('Yes')
            ),
        ));
		
		$this->addColumn('is_popup', array(
            'header' => Mage::helper('contests')->__('Popup'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'is_popup',
            'type' => 'options',
            'options' => array(
                0 => Mage::helper('contests')->__('No'),
                1 => Mage::helper('contests')->__('Yes')
            ),
        ));
		
		$this->addColumn('new_subscriber_counter', array(
            'header' => Mage::helper('contests')->__('New Subscriber Counter'),
            'align' => 'left',
            'index' => 'new_subscriber_counter',
        ));
		
		$this->addColumn('added', array(
            'header' => Mage::helper('contests')->__('Created At'),
            'index' => 'added',
            'width' => '120px',
            'type' => 'datetime',
            'gmtoffset' => true,
            'default' => ' -- '
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('contests')->__('Action'),
            'index' => 'stores',
            'sortable' => false,            
            'filter' => false,
            'width' => '100',
            'is_system' => true,
            'renderer'  => 'FactoryX_Contests_Block_Adminhtml_Template_Grid_Renderer_Action'
        ));
        
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() 
	{
        $this->setMassactionIdField('contest_id');
        $this->getMassactionBlock()->setFormFieldName('contests');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('contests')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('contests')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('contests/status')->getOptionArray();

        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('contests')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('contests')->__('Status'),
                    'values' => $statuses
                )
            )
        ));
        return $this;
    }

    public function getRowUrl($row) 
	{
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
