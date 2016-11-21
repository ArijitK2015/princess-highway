<?php

/**
 * Class FactoryX_CreditmemoReasons_Block_Adminhtml_Reason_Grid
 */
class FactoryX_CreditmemoReasons_Block_Adminhtml_Reason_Grid extends Mage_Adminhtml_Block_Widget_Grid {

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
        $collection = Mage::getModel('creditmemoreasons/reason')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return mixed
     */
    protected function _prepareColumns()
    {
        $this->addColumn('reason_id', array(
            'header' => $this->__('ID'),
            'index'  => 'reason_id',
            'width'  => '10px'
        ));

        $this->addColumn('title', array(
            'header' => $this->__('Reason Title'),
            'index'  => 'title',
            'type'   => "text"
        ));

        $this->addColumn('identifier', array(
            'header' => $this->__('Identifier'),
            'index'  => 'identifier',
            'type'   => 'text'
        ));
        
        $this->addColumn('sort_order', array(
            'header' => $this->__('Sort Order'),
            'index'  => 'sort_order',
            'type'   => 'text'
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
        $modelPk = Mage::getModel('creditmemoreasons/reason')->getResource()->getIdFieldName();
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
