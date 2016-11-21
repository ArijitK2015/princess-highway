<?php

/**
 * Class FactoryX_CategoryBanners_Block_Adminhtml_Categorybanners_Grid
 * This is the grid block
 */
class FactoryX_CategoryBanners_Block_Adminhtml_Categorybanners_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	/**
	 *	Constructor the grid
	 */
    public function __construct() 
	{
        parent::__construct();
        $this->setId('categorybannersGrid');
        $this->setDefaultSort('banner_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

	/**
	 *	Prepare the collection to display in the grid
	 */
    protected function _prepareCollection() 
	{
		// Get the collection of category banners
        $collection = Mage::getModel('categorybanners/banner')->getCollection()->addFieldToSelect(array(
                'banner_id',
                'category_id',
                'image',
                'alt',
                'start_date',
                'end_date',
                'url',
                'status',
                'display_on_children',
                'added',
                'edited'
            )
        );

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

	/**
	 *	Prepare the columns of the grid
	 */
    protected function _prepareColumns() 
	{
        $this->addColumn('banner_id', array(
            'header' => Mage::helper('categorybanners')->__('Banner #'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'banner_id',
        ));

        $this->addColumn('category_id', array(
            'header' => Mage::helper('categorybanners')->__('Category #'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'category_id',
        ));

        // Here we use a custom renderer to display the image
        $this->addColumn('image', array(
            'header' => Mage::helper('categorybanners')->__('Image'),
            'align' => 'left',
            'index' => 'image',
            'renderer'  => 'categorybanners/adminhtml_template_grid_renderer_image'
        ));
		
		$this->addColumn('alt', array(
            'header' => Mage::helper('categorybanners')->__('Alternative Text'),
            'align' => 'left',
            'index' => 'alt',
			'width' => '100px',
        ));
		
		$this->addColumn('url', array(
            'header' => Mage::helper('categorybanners')->__('URL'),
            'align' => 'left',
            'index' => 'url',
			'width' => '100px'
        ));

        // Retrieve the statuses
		$statuses = Mage::getSingleton('categorybanners/status')->getOptionArray();

        $this->addColumn('status', array(
            'header' => Mage::helper('categorybanners')->__('Status'),
            'width' => 'array',
            'index' => 'status',
			'type' => 'options',
            'options' => $statuses,
        ));

		$this->addColumn('display_on_children', array(
            'header' => Mage::helper('categorybanners')->__('Display on Children'),
            'align' => 'left',
            'index' => 'display_on_children',
            'type' => 'options',
			'width' => '100px',
            'options' => array(
                0 => Mage::helper('categorybanners')->__('No'),
				1 => Mage::helper('categorybanners')->__('Yes')
            ),
        ));

        $this->addColumn('start_date', array(
            'header' => Mage::helper('categorybanners')->__('Start Date'),
            'index' => 'start_date',
            'width' => '140px',
            'type' => 'datetime',
            'gmtoffset' => true,
            'default' => ' -- '
        ));

        $this->addColumn('end_date', array(
            'header' => Mage::helper('categorybanners')->__('End Date'),
            'index' => 'end_date',
            'width' => '140px',
            'type' => 'datetime',
            'gmtoffset' => true,
            'default' => ' -- '
        ));
		
		$this->addColumn('added', array(
            'header' => Mage::helper('categorybanners')->__('Created At'),
            'index' => 'added',
            'width' => '140px',
            'type' => 'datetime',
            'gmtoffset' => true,
            'default' => ' -- '
        ));
		
		$this->addColumn('edited', array(
            'header' => Mage::helper('categorybanners')->__('Edited At'),
            'index' => 'edited',
            'width' => '140px',
            'type' => 'datetime',
            'gmtoffset' => true,
            'default' => ' -- '
        ));

        // Here we use a custom renderer to display the action
        $this->addColumn('action', array(
            'header' => Mage::helper('categorybanners')->__('Action'),
            'index' => 'stores',
            'sortable' => false,            
            'filter' => false,
            'width' => '160',
            'is_system' => true,
			'renderer'  => 'categorybanners/adminhtml_template_grid_renderer_action'
        ));
        
        return parent::_prepareColumns();
    }

	/**
	 *	Prepare mass actions
	 */
    protected function _prepareMassaction() 
	{
        $this->setMassactionIdField('banner_id');
        $this->getMassactionBlock()->setFormFieldName('categorybanners');

		// Mass delete action
        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('categorybanners')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('categorybanners')->__('Are you sure?')
        ));

		// List of statuses
        $statuses = Mage::getSingleton('categorybanners/status')->getOptionArray();

		// Mass status change action
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('categorybanners')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('categorybanners')->__('Status'),
                    'values' => $statuses
                )
            )
        ));
        return $this;
    }

    /**
     * Getter for the row URL
     * @param $row
     * @return string
     */
    public function getRowUrl($row) 
	{
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
