<?php
/**
 * Adminhtml sales orders grid
 *
 * @author      Factory X Team <raphael@factoryx.com.au>
 */
class FactoryX_ShippedFrom_Block_Adminhtml_Sales_Shipment_Grid extends Mage_Adminhtml_Block_Sales_Shipment_Grid
{
    protected function _prepareColumns()
    {
		$this->addColumn('increment_id', array(
            'header'    => Mage::helper('sales')->__('Shipment #'),
            'index'     => 'increment_id',
            'type'      => 'text',
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('sales')->__('Date Shipped'),
            'index'     => 'created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('order_increment_id', array(
            'header'    => Mage::helper('sales')->__('Order #'),
            'index'     => 'order_increment_id',
            'type'      => 'text',
        ));

        $this->addColumn('order_created_at', array(
            'header'    => Mage::helper('sales')->__('Order Date'),
            'index'     => 'order_created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
        ));

        $this->addColumn('total_qty', array(
            'header' => Mage::helper('sales')->__('Total Qty'),
            'index' => 'total_qty',
            'type'  => 'number',
        ));
		
		$this->addColumn('shipped_from', array(
            'header' => Mage::helper('sales')->__('Shipped From'),
            'index' => 'shipped_from',
			'type' => 'options',
			'options' => Mage::helper('shippedfrom')->getStores()
        ));

        $this->addColumn('shipped_by', array(
            'header' => Mage::helper('sales')->__('Shipped By'),
            'index' => 'shipped_by',
			'type' => 'options',
			'options' => Mage::helper('shippedfrom')->getUsers(true)
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('sales')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('sales')->__('View'),
                        'url'     => array('base'=>'*/sales_shipment/view'),
                        'field'   => 'shipment_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));
		
		return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
       
	}

}
