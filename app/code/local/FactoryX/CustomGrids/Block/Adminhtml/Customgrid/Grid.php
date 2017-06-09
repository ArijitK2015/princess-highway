<?php

/**
 * Class FactoryX_CustomGrids_Block_Adminhtml_Customgrid_Grid
 */
class FactoryX_CustomGrids_Block_Adminhtml_Customgrid_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('grid_id');
        $this->setDefaultSort('sort_order');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return mixed
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('customgrids/column')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return mixed
     */
    protected function _prepareColumns()
    {
        $this->addColumn('column_id', array(
            'header' => $this->__('ID'),
            'index'  => 'column_id',
            'width'  => '10px'
        ));

        $this->addColumn('attribute_code', array(
            'header' => $this->__('Attribute Code'),
            'index'  => 'attribute_code',
            'type'   => "text"
        ));

        $this->addColumn('grid_block_type', array(
            'header' => $this->__('Grid'),
            'index'  => 'grid_block_type',
            'type'   => 'options',
            'options' => array_flip(Mage::getSingleton('customgrids/config')->getAllowedBlockTypes())
        ));

        $this->addColumn('after_column', array(
            'header' => $this->__('Add Column After'),
            'index'  => 'after_column',
            'type'   => "text"
        ));

        $this->addColumn('remove', array(
            'header' => $this->__('Removed'),
            'index'  => 'remove',
            'type'   => "options",
            'options' => array(
                0 => Mage::helper('customgrids')->__('No'),
                1 => Mage::helper('customgrids')->__('Yes')
            ),
        ));

        return parent::_prepareColumns();
    }

    /**
     * @param $row
     * @return mixed
     */
    public function getRowUrl($row)
    {
       return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $modelPk = Mage::getModel('customgrids/column')->getResource()->getIdFieldName();
        $this->setMassactionIdField($modelPk);
        $this->getMassactionBlock()->setFormFieldName('ids');
        // $this->getMassactionBlock()->setUseSelectAll(false);
        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> $this->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete'),
        ));
        return $this;
    }
    }
