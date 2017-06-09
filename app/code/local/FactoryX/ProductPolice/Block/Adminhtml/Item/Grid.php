<?php
/**
 * Who:  Alvin Nguyen
 * When: 3/10/2014
 * Why:  
 */

class FactoryX_ProductPolice_Block_Adminhtml_Item_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    /**
     *
     */
    public function __construct(){
        parent::__construct();
        $this->setId('factoryx_productpolice_adminhtml_item_grid');
        $this->setDefaultSort('item_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('factoryx_productpolice/item_collection');
        $collection->addProductData(array('name','sku','status'));
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $helper = Mage::helper('factoryx_productpolice');

        $this->addColumn('item_id', array(
            'header' => $helper->__('ID'),
            'index'  => 'item_id',
            'width'  => '20px'
        ));

        $this->addColumn('product_id', array(
            'header' => $helper->__('Product Id'),
            'index'  => 'product_id',
            'width'  => '40px'
        ));

        $this->addColumn('name', array(
            'header' => $helper->__('Name'),
            'index'  => 'name',
            'filter_index' => 'name_table.value',
            'width'  => '250px'
        ));

        $this->addColumn('sku', array(
            'header' => $helper->__('Sku'),
            'index'  => 'sku',
            'filter_index' => 'sku_table.sku',
            'width'  => '200px'
        ));

        $this->addColumn('error_message', array(
            'header' => $helper->__('Error Message'),
            'index'  => 'error_message'
        ));

        $this->addColumn('status', array(
            'header' => $helper->__('Status'),
            'index'  => 'status',
            'type'   => 'options',
            'width'  => '100px',
            'filter_index' => 'status_table.value',
            'options'=> array( 0 => 'Disable', 1 => 'Enable')
        ));

        $this->addColumn('created_at', array(
            'header'       => $helper->__('Created At'),
            'index'        => 'created_at',
            'filter_index' => 'main_table.created_at',
            'type'         => "datetime",
            'width'        => '150px'
        ));

        return parent::_prepareColumns();
    }

    /**
     * @param $row
     * @return mixed
     */
    public function getRowUrl($row)
    {
        return Mage::helper("adminhtml")->getUrl("adminhtml/catalog_product/edit",array('id'=>$row->getData('product_id')));
    }
    
    /**
     * _prepareMassaction
     *
     * adds checkbox field
     *
     * @return $this
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField('item_id');
        $this->getMassactionBlock()->setFormFieldName('item_id');
        $this->getMassactionBlock()->addItem('delete', array(
            'label'=> Mage::helper('tax')->__('Delete'),
            'url'  => $this->getUrl('*/*/massDelete', array('' => '')),
            // public function massDeleteAction() in Mage_Adminhtml_Tax_RateController
            'confirm' => Mage::helper('tax')->__('Are you sure?')
        ));
        return $this;
    }
}
