<?php

/**
 * Class FactoryX_ShippedFrom_Block_Adminhtml_Contests_Grid
 */
class FactoryX_ShippedFrom_Block_Adminhtml_Auspost_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * FactoryX_ShippedFrom_Block_Adminhtml_Auspost_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('auspostGrid');
        $this->setDefaultSort('schedule_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /** @var FactoryX_ShippedFrom_Model_Resource_Shipping_Queue_Collection $collection */
        $collection = Mage::getResourceModel('shippedfrom/shipping_queue_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'schedule_id',
            array(
                'header' => Mage::helper('shippedfrom')->__('id'),
                'width' => '10px',
                'align' => 'left',
                'index' => 'schedule_id'
            )
        );

        $this->addColumn(
            'created_at',
            array(
                'header' => Mage::helper('shippedfrom')->__('Created At'),
                'align' =>  'left',
                'width' => '140px',
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
                'index' => 'status',
                'type'  => 'options',
                'options' => $this->getStatuses()
            )
        );

        $this->addColumn(
            'shipment_id',
            array(
                'header' => Mage::helper('shippedfrom')->__('Magento Shipment #'),
                'align' =>  'left',
                'index' => 'shipment_id',
                'renderer' => 'shippedfrom/adminhtml_auspost_grid_renderer_shipment'
            )
        );

        $this->addColumn(
            'ap_shipment_id',
            array(
                'header' => Mage::helper('shippedfrom')->__('AusPost Shipment #'),
                'align' =>  'left',
                'index' => 'ap_shipment_id',
            )
        );

        $this->addColumn(
            'ap_request_id',
            array(
                'header' => Mage::helper('shippedfrom')->__('AusPost Request #'),
                'align' =>  'left',
                'index' => 'ap_request_id',
            )
        );

        $this->addColumn(
            'ap_consignment_id',
            array(
                'header' => Mage::helper('shippedfrom')->__('AusPost Consignment #'),
                'align' =>  'left',
                'index' => 'ap_consignment_id',
            )
        );

        $this->addColumn(
            'ap_order_id',
            array(
                'header' => Mage::helper('shippedfrom')->__('AusPost Order #'),
                'align' =>  'left',
                'index' => 'ap_order_id',
            )
        );

        $this->addColumn(
            'local_label_link',
            array(
                'header' => Mage::helper('shippedfrom')->__('AusPost Label URI'),
                'align' =>  'left',
                'index' => 'local_label_link',
                'renderer' => 'shippedfrom/adminhtml_auspost_grid_renderer_label'
            )
        );

        $this->addColumn(
            'shipped_from',
            array(
                'header' => Mage::helper('shippedfrom')->__('Shipped From'),
                'align' =>  'left',
                'index' => 'shipped_from',
                'type'  =>  'options',
                'options'   =>  Mage::getModel('ustorelocator/settings_locations')->toOptionHash(true),
                'renderer' => 'shippedfrom/adminhtml_auspost_grid_renderer_location'
            )
        );

        $this->addColumn(
            'ap_last_message',
            array(
                'header' => Mage::helper('shippedfrom')->__('AusPost Last Message'),
                'align' =>  'left',
                'index' => 'ap_last_message',
            )
        );

        $this->addColumn(
            'ap_last_url',
            array(
                'header' => Mage::helper('shippedfrom')->__('AusPost Last URL'),
                'align' =>  'left',
                'index' => 'ap_last_url',
                'renderer' => 'shippedfrom/adminhtml_auspost_grid_renderer_url'
            )
        );

        $this->addColumn(
            'json_request',
            array(
                'header' => Mage::helper('shippedfrom')->__('JSON Request'),
                'align' =>  'left',
                'index' => 'json_request',
                'renderer' => 'shippedfrom/adminhtml_auspost_grid_renderer_json'
            )
        );

        $this->addColumn(
            'action',
            array(
                'header' => Mage::helper('shippedfrom')->__('Action'),
                'width' => '100px',
                'sortable' => false,
                'filter' => false,
                'is_system' => true,
                'renderer'  => 'shippedfrom/adminhtml_auspost_grid_renderer_action'
            )
        );

        return parent::_prepareColumns();
    }

    /**
     * Prepare mass actions
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('schedule_id');
        $this->getMassactionBlock()->setFormFieldName('auspost');

        // Create Shipment action
        $this->getMassactionBlock()->addItem(
            'create',
            array(
                'label' => Mage::helper('shippedfrom')->__('1. Create AusPost Shipments'),
                'url' => $this->getUrl('*/*/massCreate')
            )
        );

        // Generate label action
        $this->getMassactionBlock()->addItem(
            'generate',
            array(
                'label' => Mage::helper('shippedfrom')->__('2. Generate AusPost Labels'),
                'url' => $this->getUrl('*/*/massInitLabel')
            )
        );

        // Get label action
        $this->getMassactionBlock()->addItem(
            'get',
            array(
                'label' => Mage::helper('shippedfrom')->__('3. Get AusPost Labels'),
                'url' => $this->getUrl('*/*/massGetLabel')
            )
        );

        // Send label action
        $this->getMassactionBlock()->addItem(
            'send',
            array(
                'label' => Mage::helper('shippedfrom')->__('4. Send AusPost Labels'),
                'url' => $this->getUrl('*/*/massNotify')
            )
        );

        // Send label action
        $this->getMassactionBlock()->addItem(
            'order',
            array(
                'label' => Mage::helper('shippedfrom')->__('5. Create AusPost Orders'),
                'url' => $this->getUrl('*/*/massOrder')
            )
        );

        // Delete action
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label' => Mage::helper('shippedfrom')->__('Delete Shipments'),
                'url' => $this->getUrl('*/*/massDelete'),
                'confirm' => Mage::helper('shippedfrom')->__('Are you sure?')
            )
        );

        $productIds = $this->getProductIdsOptionArray();

        // Update Shipment action
        $this->getMassactionBlock()->addItem(
            'update',
            array(
                'label' => Mage::helper('shippedfrom')->__('Update Shipments'),
                'url' => $this->getUrl('*/*/massUpdate'),
                'additional' => array(
                    'product_id' => array(
                        'name' => 'product_id',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => Mage::helper('shippedfrom')->__('Product ID'),
                        'values' => $productIds
                    )
                )
            )
        );

        return $this;
    }

    /**
     * @return array
     */
    protected function getStatuses()
    {
        return array(
            FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_INITIALIZED       => "0. " . FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_INITIALIZED,
            FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_SHIPPED           => "1. " . FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_SHIPPED,
            FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_LABEL_INITIALIZED => "2. " . FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_LABEL_INITIALIZED,
            FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_LABEL_CREATED     => "3. " . FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_LABEL_CREATED,
            FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_LABEL_SENT        => "4. " . FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_LABEL_SENT,
            FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_ORDERED           => "5. " . FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_ORDERED,
            FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_COMPLETE          => "6. " . FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_COMPLETE
        );
    }

    /**
     * @return array
     */
    protected function getProductIdsOptionArray()
    {
        $options = array();
        $collection = Mage::getResourceModel('shippedfrom/account_product_collection')
            ->addFieldToSelect('product_id');
        foreach ($collection as $product) {
            $productId = $product->getProductId();
            if (!array_key_exists($productId, $options)) {
                $options[$productId] = $productId;
            }
        }

        return $options;
    }

}