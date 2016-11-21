<?php

/**
 * Class FactoryX_CustomGrids_Model_Config_Order
 */
class FactoryX_CustomGrids_Model_Config_Order extends FactoryX_CustomGrids_Model_Config_Sales
{
    protected $_tableRelations = array(
        Mage_Sales_Model_Order::ENTITY  => array(
            'sales_flat_order'          =>  'main_table.entity_id = sfo.entity_id',
            'sales_flat_order_address'  =>  'main_table.entity_id = sfoa.parent_id AND sfoa.address_type = "shipping"',
            'sales_flat_order_payment'  =>  'main_table.entity_id = sfop.parent_id'
        )
    );

    protected $_flatAttributes = array(
        Mage_Sales_Model_Order::ENTITY  =>
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
                ),
                'sales_flat_order_address'  => array(
                    array(
                        'code'  =>  'company',
                        'label' =>  'Company',
                        'config'    =>  array(
                            'filter_index' => 'sfoa.company'
                        )
                    ),
                    array(
                        'code'  =>  'street',
                        'label' =>  'Street',
                        'config'    =>  array(
                            'filter_index' => 'sfoa.street'
                        )
                    ),
                    array(
                        'code'  =>  'city',
                        'label' =>  'City',
                        'config'    =>  array(
                            'filter_index' => 'sfoa.city'
                        )
                    ),
                    array(
                        'code'  =>  'region',
                        'label' =>  'Region',
                        'config'    =>  array(
                            'filter_index' => 'sfoa.region'
                        )
                    ),
                    array(
                        'code'  =>  'postcode',
                        'label' =>  'Postcode',
                        'config'    =>  array(
                            'filter_index' => 'sfoa.postcode'
                        )
                    ),
                    array(
                        'code'  =>  'country_id',
                        'label' =>  'Country',
                        'config'    =>  array(
                            'filter_index' => 'sfoa.country_id'
                        )
                    ),
                    array(
                        'code'  =>  'shipping_address',
                        'label' =>  'Shipping Address',
                        'sql_expr' => "concat(sfoa.street,', ',sfoa.city,', ',IFNULL(sfoa.region,''),', ',sfoa.postcode,', ',sfoa.country_id)",
                        //'sql_expr' => "concat(sfoa.company,IF (sfoa.company IS NULL OR sfoa.company='', '', ', '),sfoa.street, ', ', sfoa.city, ', ', sfoa.postcode, ' ', sfoa.region, ' ', sfoa.country_id)",
                        /*'sql_cond' => '%s.address_type = "shipping"',*/
                        'config'    => array(
                            'filter_index' => 'sfoa.shipping_address',
                            'renderer' => 'customgrids/adminhtml_renderer_address',
                            'filter' => false
                        )
                    )/*,
                    array(
                        'code'  =>  'billing_address',
                        'label' =>  'Billing Address',
                        'sql_expr' => 'concat(%s.company,IF (%s.company IS NULL OR %s.company="", "", ", "),%s.street, ", ", %s.city, ", ", %s.postcode, " ", %s.region, " ", %s.country_id)',
                        'sql_cond' => '%s.address_type = "shipping"',
                        'config'    => array(
                            'renderer' => 'customgrids/adminhtml_renderer_address',
                            'filter' => false
                        )
                    )*/
                ),
                'sales_flat_order_payment'  => array(
                    array(
                        'code'  =>  'method',
                        'label' =>  'Payment Method',
                        'config'    => array(
                            'filter_index' => 'sfop.method',
                            'type'      => 'options'
                        )
                    )
                )
            )
    );
}