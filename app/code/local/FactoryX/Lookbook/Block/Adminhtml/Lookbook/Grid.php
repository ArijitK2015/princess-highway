<?php

/**
 * Class FactoryX_Lookbook_Block_Adminhtml_Lookbook_Grid
 */
class FactoryX_Lookbook_Block_Adminhtml_Lookbook_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	/**
	 *	Constructor the grid
	 */
    public function __construct() 
	{
        parent::__construct();
        $this->setId('lookbookGrid');
        $this->setDefaultSort('lookbook_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return Mage_Core_Model_Store
     * @throws Exception
     */
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
		// Get the collection of lookbooks
        $collection = Mage::getModel('lookbook/lookbook')->getCollection()->addFieldToSelect(array(
                'lookbook_id',
                'title',
                'identifier',
                'lookbook_type',
                'category_id',
                'looks_per_page',
                'status',
                'include_in_nav',
                'sort_order',
                'added',
                'edited',
                'lookbook_facebook'
            )
        );
		
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
        $this->addColumn('lookbook_id', array(
            'header' => Mage::helper('lookbook')->__('Lookbook #'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'lookbook_id',
        ));
		
        $this->addColumn('title', array(
            'header' => Mage::helper('lookbook')->__('Title'),
            'align' => 'left',
            'index' => 'title',
        ));
		
		$this->addColumn('identifier', array(
            'header' => Mage::helper('lookbook')->__('Identifier'),
            'align' => 'left',
            'index' => 'identifier',
			'width' => '100px',
        ));
		
		$this->addColumn('lookbook_type', array(
            'header' => Mage::helper('lookbook')->__('Lookbook Type'),
            'align' => 'left',
            'index' => 'lookbook_type',
            'type' => 'options',
			'width' => '100px',
            'options' => array(
                'category' => Mage::helper('lookbook')->__('Category Lookbook'),
				'images' => Mage::helper('lookbook')->__('Images Lookbook'),
				'slideshow' => Mage::helper('lookbook')->__('Slideshow Lookbook'),
				'flipbook' => Mage::helper('lookbook')->__('Flipbook')
            ),
        ));
		
		$this->addColumn('category_id', array(
            'header' => Mage::helper('lookbook')->__('Category #'),
            'align' => 'left',
            'index' => 'category_id',
			'width' => '100px'
        ));
		
		$this->addColumn('looks_per_page', array(
            'header' => Mage::helper('lookbook')->__('Looks Per Page'),
            'align' => 'left',
            'index' => 'looks_per_page',
            'type' => 'options',
			'width' => '100px',
            'options' => array(
                'auto' => Mage::helper('lookbook')->__('Auto'),
                '1' => Mage::helper('lookbook')->__('1'),
                '2' => Mage::helper('lookbook')->__('2'),
                '3' => Mage::helper('lookbook')->__('3'),
				'4' => Mage::helper('lookbook')->__('4'),
				'5' => Mage::helper('lookbook')->__('5')
            ),
        ));
		
		$statuses = Mage::getSingleton('lookbook/status')->getOptionArray();

        $this->addColumn('status', array(
            'header' => Mage::helper('lookbook')->__('Status'),
            'width' => 'array',
            'index' => 'status',
			'type' => 'options',
            'options' => $statuses,
        ));
		
		$this->addColumn('include_in_nav', array(
            'header' => Mage::helper('lookbook')->__('Include In Nav'),
            'align' => 'left',
            'index' => 'include_in_nav',
            'type' => 'options',
			'width' => '100px',
            'options' => array(
                "no" => Mage::helper('lookbook')->__('No'),
				"before" => Mage::helper('lookbook')->__('Yes, before Category Nav'),
				"after" => Mage::helper('lookbook')->__('Yes, after Category Nav'),
				"category" => Mage::helper('lookbook')->__('Yes, under Category Nav')
            ),
        ));
		
		$this->addColumn('sort_order', array(
            'header' => Mage::helper('lookbook')->__('Sort Order'),
            'align' => 'left',
            'index' => 'sort_order',
			'width' => '100px'
        ));
		
		$this->addColumn('added', array(
            'header' => Mage::helper('lookbook')->__('Created At'),
            'index' => 'added',
            'width' => '140px',
            'type' => 'datetime',
            'gmtoffset' => true,
            'default' => ' -- '
        ));
		
		$this->addColumn('edited', array(
            'header' => Mage::helper('lookbook')->__('Edited At'),
            'index' => 'edited',
            'width' => '140px',
            'type' => 'datetime',
            'gmtoffset' => true,
            'default' => ' -- '
        ));
		
        $this->addColumn('action', array(
            'header' => Mage::helper('lookbook')->__('Action'),
            'index' => 'stores',
            'sortable' => false,            
            'filter' => false,
            'width' => '160',
            'is_system' => true,
			'renderer'  => 'lookbook/adminhtml_template_grid_renderer_action'
        ));
        
        return parent::_prepareColumns();
    }

	/**
	 *	Prepare mass actions
	 */
    protected function _prepareMassaction() 
	{
        $this->setMassactionIdField('lookbook_id');
        $this->getMassactionBlock()->setFormFieldName('lookbook');

		// Delete action
        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('lookbook')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('lookbook')->__('Are you sure?')
        ));

		// List of statuses
        $statuses = Mage::getSingleton('lookbook/status')->getOptionArray();

		// Change status action
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('lookbook')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('lookbook')->__('Status'),
                    'values' => $statuses
                )
            )
        ));
        return $this;
    }

    /**
     *    Getter for the row URL
     * @param $row
     * @return string
     */
    public function getRowUrl($row) 
	{
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
