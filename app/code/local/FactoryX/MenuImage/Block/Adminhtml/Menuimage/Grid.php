<?php

/**
 * Class FactoryX_MenuImage_Block_Adminhtml_Menuimage_Grid
 */
class FactoryX_MenuImage_Block_Adminhtml_Menuimage_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	/**
	 *	Constructor the grid
	 */
    public function __construct() 
	{
        parent::__construct();
        $this->setId('menuimageGrid');
        $this->setDefaultSort('menuimage_id');
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
		// Get the collection of home pages
        $collection = Mage::getModel('menuimage/menuimage')->getCollection()
							->addFieldToSelect(array('menuimage_id','category_id','status','added','edited'));
		
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
        $this->addColumn('menuimage_id', array(
            'header' => Mage::helper('menuimage')->__('Menu Image #'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'menuimage_id',
        ));
		
		$this->addColumn('layout', array(
            'header' => Mage::helper('menuimage')->__('Category #'),
            'align' => 'left',
			'width'	=>	'100px',
            'index' => 'category_id'
        ));
		
		$statuses = Mage::getSingleton('menuimage/status')->getOptionArray();

        $this->addColumn('status', array(
            'header' => Mage::helper('menuimage')->__('Status'),
            'width' => 'array',
            'index' => 'status',
			'type' => 'options',
            'options' => $statuses,
        ));
		
		$this->addColumn('added', array(
            'header' => Mage::helper('menuimage')->__('Created At'),
            'index' => 'added',
            'width' => '140px',
            'type' => 'datetime',
            'gmtoffset' => true,
            'default' => ' -- '
        ));
		
		$this->addColumn('edited', array(
            'header' => Mage::helper('menuimage')->__('Edited At'),
            'index' => 'edited',
            'width' => '140px',
            'type' => 'datetime',
            'gmtoffset' => true,
            'default' => ' -- '
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('menuimage')->__('Action'),
            'index' => 'stores',
            'sortable' => false,            
            'filter' => false,
            'width' => '100',
            'is_system' => true,
			'renderer'  => 'menuimage/adminhtml_template_grid_renderer_action'
        ));
        
        return parent::_prepareColumns();
    }

	/**
	 *	Prepare mass actions
	 */
    protected function _prepareMassaction() 
	{
        $this->setMassactionIdField('menuimage_id');
        $this->getMassactionBlock()->setFormFieldName('menuimage');

		// Delete action
        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('menuimage')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('menuimage')->__('Are you sure?')
        ));

		// List of statuses
        $statuses = Mage::getSingleton('menuimage/status')->getOptionArray();

		// Change status action
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('menuimage')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('menuimage')->__('Status'),
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
