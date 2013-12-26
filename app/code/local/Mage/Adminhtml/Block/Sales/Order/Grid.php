<?php
/**
	Add the following features:
	- Sort Orders by Customer Groups via customer groups column
	- company column
	- payment method
	- created by column
	- customer_email column
	- shipping address column
	- remove billing name column
	http://www.magentocommerce.com/wiki/5_-_modules_and_development/admin/sort_order_by_customer_groups
*/

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales orders grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'sales/order_grid_collection';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());

		// shipping address
		$collection->getSelect()->joinLeft(
			array('sfoa' => 'sales_flat_order_address'),
			'main_table.entity_id = sfoa.parent_id AND sfoa.address_type = "shipping"',
			array('sfoa.company', 'shipping_address' => new Zend_Db_Expr("concat(sfoa.street,', ',sfoa.city,', ',IFNULL(sfoa.region,''),', ',sfoa.postcode,', ',sfoa.country_id)"))
		);
		
		// payment method
		$collection->getSelect()->joinLeft(
			array('sfop' => 'sales_flat_order_payment'),
			'main_table.entity_id = sfop.parent_id',
			array('sfop.method')
		);

        // created by
        $collection->getSelect()->joinLeft(array('sfo'=>'sales_flat_order'),'sfo.entity_id=main_table.entity_id',array('sfo.customer_email','sfo.created_by','sfo.customer_group_id'));

        
        $this->setCollection($collection);
        //Mage::log(sprintf("SQL=%s", $collection->getSelect()));
        return parent::_prepareCollection();
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
		
		/*
        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
            'filter_index' => 'main_table.billing_name'
        ));
        */

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
            'renderer' => 'Mage_Adminhtml_Block_Sales_Order_Grid_Renderer_ShippingAddress',
            'filter_index' => 'sfoa.shipping_address',
            'filter' => false
        ));
        
		$this->addColumn('method', array(
	        'header'    => Mage::helper('sales')->__('Payment Method Name'),
	        'index'     => 'method',
	        'filter_index' => 'sfop.method',
	        'type'      => 'options',
	        'options'   => array(
	        	'verisign' => 'Credit Card',
	        	'paypal_standard' => 'PayPal - Standard',
	        	'paypal_express' => 'PayPal - Express Checkout',
	        	'free' => 'None Required')
	        /*
	        // gets them ALL
	        'options'       => Mage::helper('payment')->getPaymentMethodList(true),
	        'option_groups' => Mage::helper('payment')->getPaymentMethodList(true, true, true),
	        */
	    ));         

		// Mage_Adminhtml_Block_Widget_Grid_Column
		$this->addColumn('customer_group_id', array(
            'header'=> Mage::helper('customer')->__('Customer Group'),
            'width' => '80px',
            'index' => 'customer_group_id',
			'filter_index'	=>	'sfo.customer_group_id',
            'renderer' => 'Mage_Adminhtml_Block_Sales_Order_Grid_Renderer_CustomerGroup',
            'type' => 'options',
            'options' => Mage_Adminhtml_Block_Sales_Order_Grid_Renderer_CustomerGroup::getCustomerGroupsArray()
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
            'options' => Mage_Adminhtml_Block_Sales_Order_Grid_Renderer_StoreGroup::getUsers()
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '200px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
            'filter_index' => 'main_table.status'
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

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('order_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/cancel')) {
            $this->getMassactionBlock()->addItem('cancel_order', array(
                 'label'=> Mage::helper('sales')->__('Cancel'),
                 'url'  => $this->getUrl('*/sales_order/massCancel'),
            ));
        }

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/hold')) {
            $this->getMassactionBlock()->addItem('hold_order', array(
                 'label'=> Mage::helper('sales')->__('Hold'),
                 'url'  => $this->getUrl('*/sales_order/massHold'),
            ));
        }

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/unhold')) {
            $this->getMassactionBlock()->addItem('unhold_order', array(
                 'label'=> Mage::helper('sales')->__('Unhold'),
                 'url'  => $this->getUrl('*/sales_order/massUnhold'),
            ));
        }

        $this->getMassactionBlock()->addItem('pdfinvoices_order', array(
             'label'=> Mage::helper('sales')->__('Print Invoices'),
             'url'  => $this->getUrl('*/sales_order/pdfinvoices'),
        ));

        $this->getMassactionBlock()->addItem('pdfshipments_order', array(
             'label'=> Mage::helper('sales')->__('Print Packingslips'),
             'url'  => $this->getUrl('*/sales_order/pdfshipments'),
        ));

        $this->getMassactionBlock()->addItem('pdfcreditmemos_order', array(
             'label'=> Mage::helper('sales')->__('Print Credit Memos'),
             'url'  => $this->getUrl('*/sales_order/pdfcreditmemos'),
        ));

        $this->getMassactionBlock()->addItem('pdfdocs_order', array(
             'label'=> Mage::helper('sales')->__('Print All'),
             'url'  => $this->getUrl('*/sales_order/pdfdocs'),
        ));

        $this->getMassactionBlock()->addItem('print_shipping_label', array(
             'label'=> Mage::helper('sales')->__('Print Shipping Labels'),
             'url'  => $this->getUrl('*/sales_order_shipment/massPrintShippingLabel'),
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            return $this->getUrl('*/sales_order/view', array('order_id' => $row->getId()));
        }
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

}
