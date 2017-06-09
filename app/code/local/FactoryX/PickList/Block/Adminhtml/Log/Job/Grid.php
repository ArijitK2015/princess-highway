<?php
/**
 */
class FactoryX_PickList_Block_Adminhtml_Log_Job_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('picklistJobLogGrid');
        $this->setDefaultSort('job_id');
        $this->setDefaultDir('DESC');
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('picklist/picklist_log_job')->getCollection()->setOrder('created_at', 'DESC');;
        //Mage::helper('picklist')->log(sprintf("%s->sql=%s", __METHOD__, $collection->getSelect()) );
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();
/*
        $this->addColumn('job_id', array(
            'header'    => Mage::helper('adminhtml')->__('Id'),
            'width'     => '30px',
            'index'     => 'job_id',
        ));
*/
        $this->addColumn('created_at', array(
            'header'    => Mage::helper('adminhtml')->__('Created At'),
            'type'      => 'datetime',
            'width'     => '100px',
            'index'     => 'created_at',
        ));
        $this->addColumn('job_type', array(
            'header'    => Mage::helper('adminhtml')->__('Type'),
            'width'     => '50px',
            'index'     => 'job_type',
        ));
        $this->addColumn('source_type', array(
            'header'    => Mage::helper('adminhtml')->__('Source'),
            'width'     => '50px',
            'index'     => 'source_type',
        ));
        $this->addColumn('source_ip', array(
            'header'    => Mage::helper('adminhtml')->__('Source IP'),
            'width'     => '50px',
            'index'     => 'source_ip',
        ));
        $this->addColumn('http_request', array(
            'header'    => Mage::helper('adminhtml')->__('Request'),
            'width'     => '300px',
            'index'     => 'http_request',
            'renderer'  => 'picklist/adminhtml_renderer_json'
        ));
        $this->addColumn('output_type', array(
            'header'    => Mage::helper('adminhtml')->__('Output Type'),
            'width'     => '50px',
            'index'     => 'output_type',
        ));
        $this->addColumn('email_sent', array(
            'header'    => Mage::helper('adminhtml')->__('Email Sent'),
            'width'     => '50px',
            'index'     => 'email_sent',
            'type'      => 'options',
            'options'   => array('0' => 'No','1' => 'Yes','' => 'No')
        ));
        $this->addColumn('http_response', array(
            'header'    => Mage::helper('adminhtml')->__('Response'),
            'width'     => '200px',
            'index'     => 'http_response',
            'renderer'  => 'picklist/adminhtml_renderer_url'
        ));


        return parent::_prepareColumns();
    }


    /**
     * Row click url
     *
     * @param $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/view', array('job_id' => $row->getId()));
    }
}
