<?php

class FactoryX_Homepage_Block_Adminhtml_Homepage_Grid extends Mage_Adminhtml_Block_Widget_Grid 
{
	/**
	 *	Constructor the grid
	 */
    public function __construct() 
	{
        parent::__construct();
        $this->setId('homepageGrid');
        $this->setDefaultSort('homepage_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _getStore() 
	{
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

	/**
	 *	Prepare the collection to display in the grid
	 */
    protected function _prepareCollection() 
	{
		// Get the collection of home pages
        $collection = Mage::getModel('homepage/homepage')->getCollection()
							->addFieldToSelect(array('homepage_id','title','amount','slider','sort_order','layout','status','added','edited'));
		
		$store = $this->_getStore();
        if ($store->getId()) 
		{
            $collection->addStoreFilter($store);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

	/**
	 *	Prepare the columns of the grid
	 */
    protected function _prepareColumns() 
	{
        $this->addColumn('homepage_id', array(
            'header' => Mage::helper('homepage')->__('Home Page #'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'homepage_id',
        ));
		
		$this->addColumn('layout', array(
            'header' => Mage::helper('homepage')->__('Chosen Layout'),
            'align' => 'left',
			'width'	=>	'100px',
            'index' => 'layout',
			'renderer'  => 'FactoryX_Homepage_Block_Adminhtml_Homepage_Grid_Renderer_LayoutImage',
			'filter'	=> false,
			'sortable'	=> false,
        ));

        $this->addColumn('title', array(
            'header' => Mage::helper('homepage')->__('Title'),
            'align' => 'left',
            'index' => 'title',
        ));

        $this->addColumn('amount', array(
            'header' => Mage::helper('homepage')->__('Number of pictures'),
            'align' => 'left',
            'index' => 'amount',
			'width' => '100px',
        ));
		
		$this->addColumn('slider', array(
            'header' => Mage::helper('homepage')->__('Slider'),
            'align' => 'left',
            'index' => 'slider',
            'type' => 'options',
			'width' => '100px',
            'options' => array(
                0 => Mage::helper('homepage')->__('No'),
                1 => Mage::helper('homepage')->__('Yes')
            ),
        ));
		
		$this->addColumn('sort_order', array(
            'header' => Mage::helper('homepage')->__('Sort Order'),
            'align' => 'left',
            'index' => 'sort_order',
        ));
		
		$statuses = Mage::getSingleton('homepage/status')->getOptionArray();

        $this->addColumn('status', array(
            'header' => Mage::helper('homepage')->__('Status'),
            'width' => 'array',
            'index' => 'status',
			'type' => 'options',
            'options' => $statuses,
        ));


		// Output format for the start and end dates
		/*
		$outputFormat = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
		
        $this->addColumn('start_date', array(
            'header' => Mage::helper('homepage')->__('Start Date'),
            'index' => 'start_date',
            'type' => 'datetime',
            'width' => '120px',
			'format' => $outputFormat,
            'default' => ' -- '
        ));

        $this->addColumn('end_date', array(
            'header' => Mage::helper('homepage')->__('End Date'),
            'index' => 'end_date',
            'width' => '120px',
            'type' => 'datetime',
			'format' => $outputFormat,
            'default' => ' -- '
        ));
		*/
		
		$this->addColumn('added', array(
            'header' => Mage::helper('homepage')->__('Created At'),
            'index' => 'added',
            'width' => '140px',
            'type' => 'datetime',
            'gmtoffset' => true,
            'default' => ' -- '
        ));
		
		$this->addColumn('edited', array(
            'header' => Mage::helper('homepage')->__('Edited At'),
            'index' => 'edited',
            'width' => '140px',
            'type' => 'datetime',
            'gmtoffset' => true,
            'default' => ' -- '
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('homepage')->__('Action'),
            'index' => 'stores',
            'sortable' => false,            
            'filter' => false,
            'width' => '100',
            'is_system' => true,
			'renderer'  => 'FactoryX_Homepage_Block_Adminhtml_Template_Grid_Renderer_Action'
        ));
        
        return parent::_prepareColumns();
    }

	/**
	 *	Prepare mass actions
	 */
    protected function _prepareMassaction() 
	{
        $this->setMassactionIdField('homepage_id');
        $this->getMassactionBlock()->setFormFieldName('homepage');

		// Delete action
        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('homepage')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('homepage')->__('Are you sure?')
        ));

		// List of statuses
        $statuses = Mage::getSingleton('homepage/status')->getOptionArray();

		// Change status action
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('homepage')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('homepage')->__('Status'),
                    'values' => $statuses
                )
            )
        ));
        return $this;
    }

	/**
	 *	Getter for the row URL
	 */
    public function getRowUrl($row) 
	{
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
