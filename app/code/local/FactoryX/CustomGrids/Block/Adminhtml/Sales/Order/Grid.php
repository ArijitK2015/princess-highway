<?php
/*
 * Add several new columns to the sales order grid
 */
class FactoryX_CustomGrids_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{

    protected function _prepareCollection() {
        
        $collection = Mage::getResourceModel($this->_getCollectionClass());

		// Join sales_flat_order_address to get the shipping address
		$collection->getSelect()->joinLeft(
			array('sfoa' => 'sales_flat_order_address'),
			'main_table.entity_id = sfoa.parent_id AND sfoa.address_type = "shipping"',
			array(
			    'sfoa.company',
			    'shipping_address' => new Zend_Db_Expr(
			        "concat(sfoa.street,', ',sfoa.city,', ',IFNULL(sfoa.region,''),', ',sfoa.postcode,', ',sfoa.country_id)"
                )
            )
		);
		
		// Join sales_flat_order to get the state, shipping method, customer email, created by and customer group
        $collection->getSelect()->joinLeft(
            array('sfo' => 'sales_flat_order'),
            'main_table.entity_id=sfo.entity_id',
            array(
                'sfo.state',
                'sfo.shipping_method',
                'sfo.customer_email',
                'sfo.created_by',
                'sfo.customer_group_id'
            )
        );

		// Join sales_flat_order_payment to get the payment method
		$collection->getSelect()->joinLeft(
			array('sfop' => 'sales_flat_order_payment'),
			'main_table.entity_id = sfop.parent_id',
			array('sfop.method')
		);
        
        
        $this->setCollection($collection);
        
		// We don't call the Mage_Adminhtml_Block_Sales_Order_Grid function as it would rewrite our columns
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('real_order_id', array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
            'filter_index' => 'main_table.increment_id'
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('sales')->__('Purchased From (Store)'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view'=> true,
                'display_deleted' => true,
				'filter_index' => 'main_table.store_id'
            ));
        }

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
            'filter_index' => 'main_table.created_at'            
        ));

		$this->addColumn('customer_email', array(
			'header' => Mage::helper('sales')->__('Customer Email'),
			'index' => 'customer_email',
			'filter_index' => 'sfo.customer_email',
		));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
            'filter_index' => 'main_table.shipping_name'
        ));

		$this->addColumn('company', array(
            'header' => Mage::helper('sales')->__('Ship to Company'),
            'index' => 'company',
            'filter_index' => 'sfoa.company'
        ));
		
		$this->addColumn('shipping_address', array(
            'header' => Mage::helper('sales')->__('Ship to Address'),
            'index' => 'shipping_address',
            'renderer' => 'FactoryX_CustomGrids_Block_Adminhtml_Sales_Order_Grid_Renderer_ShippingAddress',
            'filter_index' => 'sfoa.shipping_address',
            'filter' => false
        ));

		$this->addColumn('method_ship', array(
	        'header'    => Mage::helper('sales')->__('Shipment Method'),
	        'index'     => 'shipping_method',
	        'filter_index' => 'sfo.shipping_method',
	        //'type'      => 'options',
	        'filter'    => false
	    ));         
        
		$this->addColumn('method', array(
	        'header'    => Mage::helper('sales')->__('Payment Method'),
	        'index'     => 'method',
	        'filter_index' => 'sfop.method',
	        'type'      => 'options',
	        'options'       => Mage::helper('payment')->getPaymentMethodList(true),
	    ));         

		// Mage_Adminhtml_Block_Widget_Grid_Column
		$this->addColumn('customer_group_id', array(
            'header'=> Mage::helper('customer')->__('Customer Group'),
            'width' => '80px',
            'index' => 'customer_group_id',
			'filter_index'	=>	'sfo.customer_group_id',
            'renderer' => 'FactoryX_CustomGrids_Block_Adminhtml_Sales_Order_Grid_Renderer_CustomerGroup',
            'type' => 'options',
            'options' => FactoryX_CustomGrids_Block_Adminhtml_Sales_Order_Grid_Renderer_CustomerGroup::getCustomerGroupsArray()
        ));
        
        $this->addColumn('base_grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Base)'),
            'index' => 'base_grand_total',
            'type'  => 'currency',
            'currency' => 'base_currency_code',
            'filter_index' => 'main_table.base_grand_total'
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
            'filter_index' => 'main_table.grand_total'            
        ));

        $this->addColumn('created_by', array(
            'header' => Mage::helper('sales')->__('Created By'),
            'index' => 'created_by',
            'filter_index' => 'sfo.created_by',
            'type'      => 'options',
            'options' => FactoryX_CustomGrids_Block_Adminhtml_Sales_Order_Grid_Renderer_StoreGroup::getUsers()
        ));

        $this->addColumn('state', array(
            'header'        => Mage::helper('sales')->__('State'),
            'index'         => 'state',
            'type'          => 'options',
            'width'         => '200px',
            'options'       => Mage::getSingleton('sales/order_config')->getStates(),
            'filter_index'  => 'sfo.state'
        ));

        $this->addColumn('status', array(
            'header'        => Mage::helper('sales')->__('Status'),
            'index'         => 'status',
            'type'          => 'options',
            'width'         => '200px',
            'options'       => Mage::getSingleton('sales/order_config')->getStatuses(),
            'filter_index'  => 'main_table.status'
        ));

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            $this->addColumn('action',
                array(
                    'header'    => Mage::helper('sales')->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'     => 'getId',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('sales')->__('View'),
                            'url'     => array('base'=>'*/sales_order/view'),
                            'field'   => 'order_id'
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'stores',
                    'is_system' => true,
            ));
        }
        $this->addRssList('rss/order/new', Mage::helper('sales')->__('New Order RSS'));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));

		// We don't call the Mage_Adminhtml_Block_Sales_Order_Grid function as it would rewrite our columns
        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }
}