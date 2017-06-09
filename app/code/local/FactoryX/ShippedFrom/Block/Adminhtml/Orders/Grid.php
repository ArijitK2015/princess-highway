<?php

/**
 * Class FactoryX_ShippedFrom_Block_Adminhtml_Orders_Grid
 */
class FactoryX_ShippedFrom_Block_Adminhtml_Orders_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * FactoryX_ShippedFrom_Block_Adminhtml_Orders_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('auspostOrdersGrid');
        $this->setDefaultSort('order_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /** @var FactoryX_ShippedFrom_Model_Resource_Orders_Collection $collection */
        $collection = Mage::getResourceModel('shippedfrom/orders_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'order_id',
            array(
                'header' => Mage::helper('shippedfrom')->__('Order #'),
                'align' => 'left',
                'width' => '50px',
                'index' => 'order_id',
            )
        );

        $this->addColumn(
            'created_at',
            array(
                'header' => Mage::helper('shippedfrom')->__('Created At'),
                'align' =>  'left',
                'index' => 'created_at',
                'type' => 'datetime',
                'gmtoffset' => true,
                'default' => ' -- '
            )
        );

        $this->addColumn(
            'status',
            array(
                'header' => Mage::helper('shippedfrom')->__('Status'),
                'align' => 'left',
                'width' => '50px',
                'index' => 'status',
            )
        );

        $this->addColumn(
            'ap_order_id',
            array(
                'header' => Mage::helper('shippedfrom')->__('AusPost Order #'),
                'align' => 'left',
                'width' => '50px',
                'index' => 'ap_order_id',
            )
        );

        $this->addColumn(
            'order_reference',
            array(
                'header' => Mage::helper('shippedfrom')->__('Order Reference'),
                'align' => 'left',
                'width' => '50px',
                'index' => 'order_reference',
            )
        );

        $this->addColumn(
            'ap_payment_id',
            array(
                'header' => Mage::helper('shippedfrom')->__('AusPost Payment #'),
                'align' => 'left',
                'width' => '50px',
                'index' => 'ap_payment_id',
            )
        );

        $this->addColumn(
            'merchant_location_id',
            array(
                'header' => Mage::helper('shippedfrom')->__('Merchant Location Id'),
                'align' => 'left',
                'width' => '50px',
                'index' => 'merchant_location_id',
            )
        );

        $this->addColumn(
            'charge_account',
            array(
                'header' => Mage::helper('shippedfrom')->__('Charge Account'),
                'align' => 'left',
                'width' => '50px',
                'index' => 'charge_account',
            )
        );

        $this->addColumn(
            'number_of_shipments',
            array(
                'header' => Mage::helper('shippedfrom')->__('Number of Shipments'),
                'align' => 'left',
                'width' => '50px',
                'index' => 'number_of_shipments',
                'type' => 'number'
            )
        );

        $this->addColumn(
            'total_cost',
            array(
                'header' => Mage::helper('shippedfrom')->__('Total Cost'),
                'align' => 'left',
                'width' => '50px',
                'index' => 'total_cost',
                'type' => 'number'
            )
        );        

        $this->addColumn(
            'action',
            array(
                'header' => Mage::helper('shippedfrom')->__('Action'),
                'sortable' => false,
                'filter' => false,
                'is_system' => true,
                'renderer'  => 'shippedfrom/adminhtml_orders_grid_renderer_action'
            )
        );

        return parent::_prepareColumns();
    }

}