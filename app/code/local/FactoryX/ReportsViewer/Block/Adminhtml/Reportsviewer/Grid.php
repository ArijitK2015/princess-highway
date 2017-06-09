<?php

/**
 * Class FactoryX_Reportsviewer_Block_Adminhtml_Reportsviewer_Grid
 * This is the block representing the grid of reports
 */
class FactoryX_Reportsviewer_Block_Adminhtml_Reportsviewer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     *	Constructor the grid
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('reportsviewerGrid');
        $this->setDefaultSort('added','DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     *	Prepare the collection to display in the grid
     */
    protected function _prepareCollection()
    {
        // Create a collection
        $collection = Mage::getSingleton('reportsviewer/reports_collection');

        // We set the collection of the grid
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     *	Prepare the columns of the grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('report_id', array(
            'header' => Mage::helper('reportsviewer')->__('Report #'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'report_id',
        ));

        $this->addColumn('error', array(
            'header' => Mage::helper('reportsviewer')->__('Error'),
            'align' => 'right',
            'index' => 'error',
        ));

        $this->addColumn('url', array(
            'header' => Mage::helper('reportsviewer')->__('URL'),
            'align' => 'right',
            'index' => 'url',
        ));

        $this->addColumn('script_name', array(
            'header' => Mage::helper('reportsviewer')->__('Script Name'),
            'align' => 'right',
            'index' => 'script_name',
        ));

        $this->addColumn('skin', array(
            'header' => Mage::helper('reportsviewer')->__('Skin'),
            'align' => 'right',
            'index' => 'skin',
        ));

        $this->addColumn('file', array(
            'header' => Mage::helper('reportsviewer')->__('File'),
            'align' => 'right',
            'index' => 'file',
        ));

        $this->addColumn('added', array(
            'header' => Mage::helper('reportsviewer')->__('Created At'),
            'index' => 'added',
            'width' => '140px',
            'type' => 'datetime'
        ));

        // Here we use a custom renderer to be able to display what we want
        $this->addColumn('action', array(
            'header' => Mage::helper('reportsviewer')->__('Action'),
            'index' => 'stores',
            'sortable' => false,
            'filter' => false,
            'width' => '160',
            'is_system' => true,
            'renderer'  => 'reportsviewer/adminhtml_template_grid_renderer_action'
        ));

        return $this;
    }

    /**
     *	Prepare mass actions
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('ids');

        // Delete action
        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('reportsviewer')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('reportsviewer')->__('Are you sure?')
        ));

        return $this;
    }

    /**
     *  Getter for the row URL
     *  @param $row
     *  @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getData('report_id')));
    }
}
