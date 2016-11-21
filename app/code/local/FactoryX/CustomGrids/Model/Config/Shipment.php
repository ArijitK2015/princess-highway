<?php

/**
 * Class FactoryX_CustomGrids_Model_Config_Invoice
 */
class FactoryX_CustomGrids_Model_Config_Shipment extends FactoryX_CustomGrids_Model_Config_Sales
{
    protected $_tableRelations = array(
        Mage_Sales_Model_Order_Shipment::HISTORY_ENTITY_NAME => array(
            'sales_flat_order'  =>  'main_table.order_id = sfo.entity_id'
        )
    );

    protected $_flatAttributes = array(
        Mage_Sales_Model_Order_Shipment::HISTORY_ENTITY_NAME  =>
            array(
                'sales_flat_order' => array(
                    array(
                        'code'    => 'coupon_code',
                        'label'   => 'Coupon Code'
                    ),
                    array(
                        'code'  =>  'shipping_method',
                        'label' =>  'Shipping Method',
                        'config'    => array(
                            'filter_index' => 'sfo.shipping_method'
                        )
                    ),
                    array(
                        'code'  =>  'customer_id',
                        'label' =>  'Customer Id'
                    ),
                    array(
                        'code'  =>  'customer_email',
                        'label' =>  'Customer Email',
                        'config'    =>  array(
                            'filter_index' => 'sfo.customer_email'
                        )
                    ),
                    array(
                        'code'  =>  'created_by',
                        'label' =>  'Created By',
                        'config'    => array(
                            'filter_index' => 'sfo.created_by',
                            'type'      => 'options'
                        )
                    ),
                    array(
                        'code'  =>  'state',
                        'label' =>  'State',
                        'config'    => array(
                            'type'          => 'options',
                            'filter_index'  => 'sfo.state'
                        )
                    ),
                    array(
                        'code'  =>  'customer_group_id',
                        'label' =>  'Customer Group',
                        'config'    => array(
                            'filter_index'	=>	'sfo.customer_group_id',
                            'renderer' => 'customgrids/adminhtml_renderer_customerGroup',
                            'type' => 'options'
                        )
                    )
                )
            )
    );
}