<?php
/**
 * Sales Report Grid
 */
class FactoryX_AWAdvancedreportsMod_Block_Advanced_Sales_Grid extends AW_Advancedreports_Block_Advanced_Sales_Grid
{
    /**
     * Prepare array with collected data
     *
     * @param datetime $from
     * @param datetime $to
     * @return array
     */
    public function getPreparedData($from, $to)
    {
        /** @var AW_Advancedreports_Model_Mysql4_Collection_Sales $collection  */
        $collection = Mage::getResourceModel('advancedreports/collection_sales');
        $collection->reInitSelect();

        $collection->setDateFilter($from, $to)->setState();
        $storeIds = $this->getStoreIds();
        if (count($storeIds)) {
            $collection->setStoreFilter($storeIds);
        }

        $collection->addOrderItems($this->getCustomOption(self::OPTION_SALES_GROUPED_SKU))
            ->addCustomerInfo()
            ->addManufacturer()
            ->addOriginalPrice()
            ->addAddress();

        if (!$this->getCustomOption('include_refunded')) {
            $collection->excludeRefunded();
        }

        $this->setCollection($collection);
        $this->_prepareData();

        return $this->getCustomVarData();
    }

    /*
    SELECT `main_table`.*, `main_table`.* FROM `aw_arep_aggregated_4480a4f8b6be2729548afc04e585a082` AS `main_table`
    WHERE (main_table.period_key <= '2014-04-10 13:59:59') AND (main_table.period_key >= '2014-03-31 14:00:00')
    */
    public function _prepareCollection()
    {
        $this
            ->_setUpReportKey()
            ->_setUpFilters();

        # Start aggregator
        $date_from = $this->_getMysqlFromFormat($this->getFilter('report_from'));
        $date_to = $this->_getMysqlToFormat($this->getFilter('report_to'));

        // temp
        $this->getAggregator()->cleanCache();

        // AW_Advancedreports_Helper_Tools_Aggregator
        $this->getAggregator()->prepareAggregatedCollection($date_from, $date_to);

        /** @var AW_Advancedreports_Model_Mysql4_Cache_Collection $collection  */
        $collection = $this->getAggregator()->getAggregatetCollection();

        $this->setCollection($collection);

        if ($sort = $this->_getSort()) {
            $collection->addOrder($sort, $this->_getDir());
            $this->getColumn($sort)->setDir($this->_getDir());
        }
        else {
            //$collection->addOrder('order_created_at', 'DESC');
        }
        $this->_saveFilters();
        $this->_setColumnFilters();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $def_value = sprintf("%f", 0);
        $def_value = Mage::app()->getLocale()->currency($this->getCurrentCurrencyCode())->toCurrency($def_value);

        $this->addColumn('order_increment_id', array(
            'header' => $this->_helper()->__('Order #'),
            'index' => 'order_increment_id',
            'type' => 'text',
            'width' => '80px',
            'ddl_type' => Varien_Db_Ddl_Table::TYPE_VARCHAR,
            'ddl_size' => 255,
            'ddl_options' => array('nullable' => true),
        ));

        $this->addColumn('order_created_at', array(
            'header' => $this->_helper()->__('Order Date'),
            'index' => 'order_created_at',
            'type' => 'datetime',
            'width' => '140px',
            'is_period_key' => true,
            'ddl_type' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            'ddl_size' => null,
            'ddl_options' => array('nullable' => false),
        ));

        $this->addColumn('xsku', array(
            'header' => $this->_helper()->__('SKU'),
            'width' => '120px',
            'index' => 'xsku',
            'type' => 'text',
            'ddl_type' => Varien_Db_Ddl_Table::TYPE_VARCHAR,
            'ddl_size' => 255,
            'ddl_options' => array('nullable' => false),
        ));

        $this->addColumn('customer_email', array(
            'header' => $this->_helper()->__('Customer Email'),
            'index' => 'customer_email',
            'type' => 'text',
            'width' => '100px',
            'ddl_type' => Varien_Db_Ddl_Table::TYPE_VARCHAR,
            'ddl_size' => 255,
            'ddl_options' => array('nullable' => true),
        ));

        $this->addColumn('customer_group', array(
            'header' => $this->_helper()->__('Customer Group'),
            'index' => 'customer_group',
            'type' => 'text',
            'width' => '100px',
            'ddl_type' => Varien_Db_Ddl_Table::TYPE_VARCHAR,
            'ddl_size' => 255,
            'ddl_options' => array('nullable' => true),
        ));

        $this->addColumn('order_ship_country', array(
            'header' => $this->_helper()->__('Country'),
            'index' => 'order_ship_country',
            'type' => 'country',
            'width' => '100px',
            'ddl_type' => Varien_Db_Ddl_Table::TYPE_VARCHAR,
            'ddl_size' => 10,
            'ddl_options' => array('nullable' => true),
        ));

        $this->addColumn('order_ship_region', array(
            'header' => $this->_helper()->__('Region'),
            'index' => 'order_ship_region',
            'type' => 'text',
            'width' => '100px',
            'ddl_type' => Varien_Db_Ddl_Table::TYPE_VARCHAR,
            'ddl_size' => 255,
            'ddl_options' => array('nullable' => true),
        ));

        $this->addColumn('order_ship_city', array(
            'header' => $this->_helper()->__('City'),
            'index' => 'order_ship_city',
            'type' => 'text',
            'width' => '100px',
            'ddl_type' => Varien_Db_Ddl_Table::TYPE_VARCHAR,
            'ddl_size' => 255,
            'ddl_options' => array('nullable' => true),
        ));

        $this->addColumn('order_ship_postcode', array(
            'header' => $this->_helper()->__('Zip Code'),
            'index' => 'order_ship_postcode',
            'type' => 'text',
            'width' => '60px',
            'ddl_type' => Varien_Db_Ddl_Table::TYPE_VARCHAR,
            'ddl_size' => 255,
            'ddl_options' => array('nullable' => true),
        ));

        $this->addColumn('name', array(
            'header' => $this->_helper()->__('Product Name'),
            'index' => 'name',
            'type' => 'text',
            'ddl_type' => Varien_Db_Ddl_Table::TYPE_VARCHAR,
            'ddl_size' => 255,
            'ddl_options' => array('nullable' => true),
        ));

        $this->addColumn('product_manufacturer', array(
            'header' => $this->_helper()->__('Manufacturer'),
            'index' => 'product_manufacturer',
            'type' => 'text',
            'width' => '100px',
            'ddl_type' => Varien_Db_Ddl_Table::TYPE_VARCHAR,
            'ddl_size' => 255,
            'ddl_options' => array('nullable' => true),
        ));

        $this->addColumn('xqty_ordered', array(
            'header' => $this->_helper()->__('Qty. Ordered'),
            'width' => '60px',
            'index' => 'xqty_ordered',
            'total' => 'sum',
            'type' => 'number',
            'ddl_type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'ddl_size' => '12,4',
            'ddl_options' => array('nullable' => true),
        ));

        $this->addColumn('xqty_invoiced', array(
            'header' => $this->_helper()->__('Qty. Invoiced'),
            'width' => '60px',
            'index' => 'xqty_invoiced',
            'total' => 'sum',
            'type' => 'number',
            'ddl_type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'ddl_size' => '12,4',
            'ddl_options' => array('nullable' => true),

        ));

        $this->addColumn('xqty_shipped', array(
            'header' => $this->_helper()->__('Qty. Shipped'),
            'width' => '60px',
            'index' => 'xqty_shipped',
            'total' => 'sum',
            'type' => 'number',
            'ddl_type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'ddl_size' => '12,4',
            'ddl_options' => array('nullable' => true),
        ));

        $this->addColumn('xqty_refunded', array(
            'header' => $this->_helper()->__('Qty. Refunded'),
            'width' => '60px',
            'index' => 'xqty_refunded',
            'total' => 'sum',
            'type' => 'number',
            'ddl_type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'ddl_size' => '12,4',
            'ddl_options' => array('nullable' => true),
        ));

        $this->addColumn('base_xprice2', array(
            'header' => $this->_helper()->__('Price'),
            'width' => '80px',
            'type' => 'currency',
            'currency_code' => $this->getCurrentCurrencyCode(),
            'total' => 'sum',
            'index' => 'product_price',
            'column_css_class' => 'nowrap',
            'default' => "0", // $def_value
            'disable_total' => 1,
            'ddl_type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'ddl_size' => '12,4',
            'ddl_options' => array('nullable' => true),
        ));

        $this->addColumn('base_xprice', array(
            'header' => $this->_helper()->__('Sale Price'),
            'width' => '80px',
            'type' => 'currency',
            'currency_code' => $this->getCurrentCurrencyCode(),
            'total' => 'sum',
            'index' => 'base_xprice',
            'column_css_class' => 'nowrap',
            'default' => $def_value,
            'disable_total' => 1,
            'ddl_type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'ddl_size' => '12,4',
            'ddl_options' => array('nullable' => true),
        ));

        $this->addColumn('base_row_subtotal', array(
            'header' => $this->_helper()->__('Subtotal'),
            'width' => '80px',
            'type' => 'currency',
            'currency_code' => $this->getCurrentCurrencyCode(),
            'total' => 'sum',
            'index' => 'base_row_subtotal',
            'column_css_class' => 'nowrap',
            'default' => $def_value,
            'ddl_type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'ddl_size' => '12,4',
            'ddl_options' => array('nullable' => true),
        ));

        $this->addColumn('base_tax_amount', array(
            'header' => $this->_helper()->__('Tax'),
            'width' => '80px',
            'type' => 'currency',
            'currency_code' => $this->getCurrentCurrencyCode(),
            'total' => 'sum',
            'index' => 'base_tax_amount',
            'column_css_class' => 'nowrap',
            'default' => $def_value,
            'ddl_type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'ddl_size' => '12,4',
            'ddl_options' => array('nullable' => true),
        ));

        $this->addColumn('base_discount_amount', array(
            'header' => $this->_helper()->__('Discounts'),
            'width' => '80px',
            'type' => 'currency',
            'currency_code' => $this->getCurrentCurrencyCode(),
            'total' => 'sum',
            'index' => 'base_discount_amount',
            'column_css_class' => 'nowrap',
            'default' => $def_value,
            'ddl_type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'ddl_size' => '12,4',
            'ddl_options' => array('nullable' => true),
        ));

        $this->addColumn('base_tax_amount', array(
            'header' => $this->_helper()->__('Tax'),
            'width' => '80px',
            'type' => 'currency',
            'currency_code' => $this->getCurrentCurrencyCode(),
            'total' => 'sum',
            'index' => 'base_tax_amount',
            'column_css_class' => 'nowrap',
            'default' => $def_value,
            'ddl_type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'ddl_size' => '12,4',
            'ddl_options' => array('nullable' => true),
        ));


        $this->addColumn('base_row_xtotal', array(
            'header' => $this->_helper()->__('Total'),
            'width' => '80px',
            'type' => 'currency',
            'currency_code' => $this->getCurrentCurrencyCode(),
            'total' => 'sum',
            'index' => 'base_row_xtotal',
            'column_css_class' => 'nowrap',
            'default' => $def_value,
            'ddl_type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'ddl_size' => '12,4',
            'ddl_options' => array('nullable' => true),
        ));

        $this->addColumn('base_row_xtotal_incl_tax', array(
            'header' => $this->_helper()->__('Total Incl. Tax'),
            'width' => '80px',
            'type' => 'currency',
            'currency_code' => $this->getCurrentCurrencyCode(),
            'total' => 'sum',
            'index' => 'base_row_xtotal_incl_tax',
            'column_css_class' => 'nowrap',
            'default' => $def_value,
            'ddl_type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'ddl_size' => '12,4',
            'ddl_options' => array('nullable' => true),
        ));

        $this->addColumn('base_row_xinvoiced', array(
            'header' => $this->_helper()->__('Invoiced'),
            'width' => '80px',
            'type' => 'currency',
            'currency_code' => $this->getCurrentCurrencyCode(),
            'total' => 'sum',
            'index' => 'base_row_xinvoiced',
            'column_css_class' => 'nowrap',
            'default' => $def_value,
            'ddl_type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'ddl_size' => '12,4',
            'ddl_options' => array('nullable' => true),
        ));

        $this->addColumn('base_tax_invoiced', array(
            'header' => $this->_helper()->__('Tax Invoiced'),
            'width' => '80px',
            'type' => 'currency',
            'currency_code' => $this->getCurrentCurrencyCode(),
            'total' => 'sum',
            'index' => 'base_tax_invoiced',
            'column_css_class' => 'nowrap',
            'default' => $def_value,
            'ddl_type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'ddl_size' => '12,4',
            'ddl_options' => array('nullable' => true),
        ));

        $this->addColumn('base_row_xinvoiced_incl_tax', array(
            'header' => $this->_helper()->__('Invoiced Incl. Tax'),
            'width' => '80px',
            'type' => 'currency',
            'currency_code' => $this->getCurrentCurrencyCode(),
            'total' => 'sum',
            'index' => 'base_row_xinvoiced_incl_tax',
            'column_css_class' => 'nowrap',
            'default' => $def_value,
            'ddl_type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'ddl_size' => '12,4',
            'ddl_options' => array('nullable' => true),
        ));

        $this->addColumn('base_row_xrefunded', array(
            'header' => $this->_helper()->__('Refunded'),
            'width' => '80px',
            'type' => 'currency',
            'currency_code' => $this->getCurrentCurrencyCode(),
            'total' => 'sum',
            'index' => 'base_row_xrefunded',
            'column_css_class' => 'nowrap',
            'default' => $def_value,
            'ddl_type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'ddl_size' => '12,4',
            'ddl_options' => array('nullable' => true),
        ));

        $this->addColumn('base_tax_xrefunded', array(
            'header' => $this->_helper()->__('Tax Refunded'),
            'width' => '80px',
            'type' => 'currency',
            'currency_code' => $this->getCurrentCurrencyCode(),
            'total' => 'sum',
            'index' => 'base_tax_xrefunded',
            'column_css_class' => 'nowrap',
            'default' => $def_value,
            'ddl_type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'ddl_size' => '12,4',
            'ddl_options' => array('nullable' => true),
        ));

        $this->addColumn('base_row_xrefunded_incl_tax', array(
            'header' => $this->_helper()->__('Refunded Incl. Tax'),
            'width' => '80px',
            'type' => 'currency',
            'currency_code' => $this->getCurrentCurrencyCode(),
            'total' => 'sum',
            'index' => 'base_row_xrefunded_incl_tax',
            'column_css_class' => 'nowrap',
            'default' => $def_value,
            'ddl_type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'ddl_size' => '12,4',
            'ddl_options' => array('nullable' => true),
        ));

        $this->addColumn('view_order',
            array(
                'header' => $this->_helper()->__('View Order'),
                'width' => '70px',
                'type' => 'action',
                'align' => 'left',
                'getter' => 'getOrderId',
                'actions' => array(
                    array(
                        'caption' => $this->_helper()->__('View'),
                        'url' => array(
                            'base' => 'adminhtml/sales_order/view',
                            'params' => array()
                        ),
                        'field' => 'order_id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'order_id',
                'ddl_type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'ddl_size' => null,
                'ddl_options' => array('nullable' => true, 'unsigned' => true),
            ));

        $this->addColumn('view_product',
            array(
                'header' => $this->_helper()->__('View Product'),
                'width' => '70px',
                'type' => 'action',
                'align' => 'left',
                'getter' => 'getProductId',
                'actions' => array(
                    array(
                        'caption' => $this->_helper()->__('View'),
                        'url' => array(
                            'base' => 'adminhtml/catalog_product/edit',
                            'params' => array()
                        ),
                        'field' => 'id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'product_id',
                'ddl_type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'ddl_size' => null,
                'ddl_options' => array('nullable' => true, 'unsigned' => true),
            ));

        $this->addExportType('*/*/exportOrderedCsv', $this->_helper()->__('CSV'));
        $this->addExportType('*/*/exportOrderedExcel', $this->_helper()->__('Excel'));
        return $this;
    }
}
