<?php
 
class Xigmapro_Jobs_Block_Adminhtml_Jobs_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('JobsGrid');
        // This is the primary key of the database
        $this->setDefaultSort('Jobs_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
 
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('Jobs/Jobs')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
 
    protected function _prepareColumns()
    {
        $this->addColumn('Jobs_id', array(
            'header'    => Mage::helper('Jobs')->__('Job ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'Jobs_id',
        ));
 
        $this->addColumn('position', array(
            'header'    => Mage::helper('Jobs')->__('Job Title'),
            'align'     =>'left',
            'index'     => 'position',
        ));
        $this->addColumn('countrys', array(
            'header'    => Mage::helper('Jobs')->__('Country'),
            'align'     =>'left',
            'index'     => 'countrys',
        ));
		 $this->addColumn('locations', array(
            'header'    => Mage::helper('Jobs')->__('Location'),
            'align'     =>'left',
            'index'     => 'locations',
        ));
        /*
        $this->addColumn('content', array(
            'header'    => Mage::helper('Jobs')->__('Item Content'),
            'width'     => '150px',
            'index'     => 'content',
        ));
        */
 
        /*$this->addColumn('created_time', array(
            'header'    => Mage::helper('Jobs')->__('Creation Time'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'default'   => '--',
            'index'     => 'created_time',
        ));
 
        $this->addColumn('update_time', array(
            'header'    => Mage::helper('Jobs')->__('Update Time'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'default'   => '--',
            'index'     => 'update_time',
        ));   */
 $this->addColumn('statuss', array(
 
            'header'    => Mage::helper('Jobs')->__('Job Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'statuss',
            'type'      => 'options',
            'options'   => array(
                1 => 'Full Time',
                0 => 'Part Time',
				2 => 'Casual',
				3 => 'Contract',
            ),
        ));
 
        $this->addColumn('status', array(
 
            'header'    => Mage::helper('Jobs')->__('Status'),
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