<?php
/**
 */

class FactoryX_PickList_Block_Adminhtml_Log_Request_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('picklistRequestLogGrid');
        $this->setDefaultSort('request_id');
        $this->setDefaultDir('DESC');
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('picklist/picklist_log_request')->getCollection()->setOrder('created_at', 'DESC');;
        Mage::helper('picklist')->log(sprintf("%s->sql=%s", __METHOD__, $collection->getSelect()) );
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
        $this->addColumn('request_id', array(
            'header'    => Mage::helper('adminhtml')->__('Id'),
            'width'     => '30px',
            'index'     => 'request_id',
        ));
*/
        $this->addColumn('created_at', array(
            'header'    => Mage::helper('adminhtml')->__('Created At'),
            'type'      => 'datetime',
            'width'     => '60px',
            'index'     => 'created_at',
        ));
        $this->addColumn('source_client', array(
            'header'    => Mage::helper('adminhtml')->__('Source Client'),
            'width'     => '120px',
            'index'     => 'source_client',
        ));
        $this->addColumn('source_ip', array(
            'header'    => Mage::helper('adminhtml')->__('Source IP'),
            'width'     => '60px',
            'index'     => 'source_ip',
        ));
        $this->addColumn('http_request', array(
            'header'    => Mage::helper('adminhtml')->__('Request'),
            'width'     => '160px',
            'index'     => 'http_request',
        ));
        $this->addColumn('response_code', array(
            'header'    => Mage::helper('adminhtml')->__('Response Code'),
            'width'     => '60px',
            'index'     => 'response_code',
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
        return $this->getUrl('*/*/view', array('request_id' => $row->getId()));
    }
}
