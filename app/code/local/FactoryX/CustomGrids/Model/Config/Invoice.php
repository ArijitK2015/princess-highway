<?php

/**
 * Class FactoryX_CustomGrids_Model_Config_Invoice
 */
class FactoryX_CustomGrids_Model_Config_Invoice extends FactoryX_CustomGrids_Model_Config_Sales
{
    protected $_tableAlias = array(
        'sales_flat_invoice' => 'sfi',
        'sales_flat_order' => 'sfo'
    );

    protected $_tableRelations = array(
        Mage_Sales_Model_Order_Invoice::HISTORY_ENTITY_NAME => array(
            'sales_flat_invoice' => 'main_table.entity_id = sfi.entity_id',
            'sales_flat_order' => 'main_table.order_id = sfo.entity_id'
        )
    );

    protected $_flatAttributes = array(
        Mage_Sales_Model_Order_Invoice::HISTORY_ENTITY_NAME  =>
            array(
                'sales_flat_invoice' => array(
                    array(
                        'code'  =>  'base_grand_total',
                        'label' =>  'Base Grand Total',
                        'config'    => array(
                            'type'          => 'currency',
                            'filter_index'  => 'sfi.base_grand_total'
                        )
                    ),
                    array(
                        'code'  =>  'shipping_amount',
                        'label' =>  'Shipping Amount',
                        'config'    => array(
                            'type'          => 'currency',
                            'filter_index'  => 'sfi.shipping_amount'
                        )
                    )
                    /*
                    TODO: add column to display the total qty of items ordered
                    array(
                        'code'  =>  'item_count',
                        'label' =>  'Item Count',
                        'config'    => array(
                            'filter_index'	=> 'sfi.item_count',
                            'type'          => 'number'
                        )
                    )
                    */
                ),
                'sales_flat_order' => array(
                    array(
                        'code'  =>  'customer_group_id',
                        'label' =>  'Customer Group',
                        'config'    => array(
                            'filter_index'  => 'sfo.customer_group_id',
                            'renderer'      => 'customgrids/adminhtml_renderer_customerGroup',
                            'type'          => 'options'
                        )
                    )
                )
            )
    );
}