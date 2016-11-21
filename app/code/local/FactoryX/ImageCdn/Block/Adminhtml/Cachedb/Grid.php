<?php
/**
 * Who:  Alvin Nguyen
 * When: 25/09/2014
 * Why:  ImageCDN grid
 */

class FactoryX_ImageCdn_Block_Adminhtml_Cachedb_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    /**
     *
     */
    public function __construct(){
        parent::__construct();
        $this->setId('factoryx_imagecdn_adminhtml_cachedb_grid');
        $this->setDefaultSort('cachedb_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('imagecdn/cachedb_collection');
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * @return mixed
     */
    protected function _prepareColumns()
    {
        $helper = Mage::helper('imagecdn');

        $this->addColumn('cachedb_id', array(
            'header' => $helper->__('ID'),
            'index'  => 'cachedb_id'
        ));

        $this->addColumn('url', array(
            'header' => $helper->__('URL'),
            'index'  => 'url'
        ));

        $this->addColumn('url_referrer', array(
            'header' => $helper->__('URL Referrer'),
            'index'  => 'url_referrer'
        ));

        $this->addColumn('last_checked', array(
            'header'       => $helper->__('Last Checked'),
            'index'        => 'last_checked',
            'type'         => "datetime"
        ));

        $this->addColumn('http_code', array(
            'header'       => $helper->__('HTTP Code'),
            'index'        => 'http_code'
        ));

        $this->addColumn('last_upload', array(
            'header'       => $helper->__('Last Upload'),
            'index'        => 'last_upload',
            'type'         => "datetime"
        ));

//        $this->addExportType('*/*/exportInchooCsv', $helper->__('CSV'));
//        $this->addExportType('*/*/exportInchooExcel', $helper->__('Excel XML'));

        return parent::_prepareColumns();
    }

    public function _prepareMassaction(){
        $helper = Mage::helper('imagecdn');
        $this->setMassactionIdField('reupload_id');
        $this->getMassactionBlock()->setFormFieldName('reupload');
        $this->getMassactionBlock()->addItem('reupload', array(
            'label' => $helper->__('Re-upload'),
            'url' => $this->getUrl('*/*/massReupload')
        ));
    }
}