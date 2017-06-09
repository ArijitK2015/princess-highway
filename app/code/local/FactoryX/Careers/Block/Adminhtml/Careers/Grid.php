<?php
 
class FactoryX_Careers_Block_Adminhtml_Careers_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('careersGrid');
        // This is the primary key of the database
        $this->setDefaultSort('careers_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
 
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('careers/careers')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
 
    protected function _prepareColumns()
    {
        $this->addColumn('careers_id', array(
            'header'    => Mage::helper('careers')->__('Career ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'careers_id',
        ));
 
        $this->addColumn('position', array(
            'header'    => Mage::helper('careers')->__('Career Title'),
            'align'     =>'left',
            'index'     => 'position',
        ));
        
        $this->addColumn('area', array(
            'header'    => Mage::helper('careers')->__('Area'),
            'align'     =>'left',
            'index'     => 'area',
            'width'     => '80px'
        ));
        
        $this->addColumn('sort', array(
            'header'    => Mage::helper('careers')->__('Sort Order'),
            'align'     => 'left',
            'index'     => 'sort',
            'width'     => '50px'
        ));
        
        $this->addColumn('locations', array(
            'header'    => Mage::helper('careers')->__('Location'),
            'align'     =>'left',
            'index'     => 'locations',
        ));
        
        $this->addColumn('email', array(
            'header'    => Mage::helper('careers')->__('Link to'),
            'align'     => 'left',
            'index'     => 'email',
        ));
        
        $this->addColumn('work_type', array(
            'header'    => Mage::helper('careers')->__('Work Type'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'work_type'
        ));
 
        $this->addColumn('status', array(
            'header'    => Mage::helper('careers')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => array(
                1 => 'Active',
                0 => 'Inactive',
            ),
        ));
 
        return parent::_prepareColumns();
    }
 
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}