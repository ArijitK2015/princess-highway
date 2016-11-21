<?php
/**
 * Bestsellers Report Grid
 */
class FactoryX_AWAdvancedreportsMod_Block_Advanced_Bestsellers_Grid extends AW_Advancedreports_Block_Advanced_Bestsellers_Grid
{
    protected function _prepareCollection()
    {
        parent::_prepareCollection();

        /** @var AW_Advancedreports_Model_Mysql4_Collection_Bestsellers $collection  */
        $collection = Mage::getResourceModel('advancedreports/collection_bestsellers');

        /**** START CUSTOM FACTORY X CODE ****/
        // Get the attribute id for the price and special price attributes
        $eavAttribute = new Mage_Eav_Model_Mysql4_Entity_Attribute();
        $priceId = $eavAttribute->getIdByCode('catalog_product', 'price');
        $eavAttribute = new Mage_Eav_Model_Mysql4_Entity_Attribute();
        $specialPriceId = $eavAttribute->getIdByCode('catalog_product', 'special_price');

        // Add price
        $collection->getSelect()
            ->joinInner(
                array('catalog_price'	=>	'catalog_product_entity_decimal'),
                'catalog_price.entity_id = e.entity_id AND catalog_price.attribute_id = '.$priceId,
                array('value AS price'));

        // Add special price
        $saleFilter = $this->getRequest()->getParam('filter_sale');
        $nonsaleFilter = $this->getRequest()->getParam('filter_nonsale');
        if ($saleFilter)
        {
            $collection->getSelect()
                ->joinInner(
                    array('catalog_sprice'	=>	'catalog_product_entity_decimal'),
                    'catalog_sprice.entity_id = e.entity_id AND catalog_sprice.value IS NOT NULL AND catalog_sprice.attribute_id = '.$specialPriceId,
                    array('value AS special_price'));
        }
        elseif ($nonsaleFilter)
        {
            $collection->getSelect()
                ->joinInner(
                    array('catalog_sprice'	=>	'catalog_product_entity_decimal'),
                    'catalog_sprice.entity_id = e.entity_id AND catalog_sprice.value IS NULL AND catalog_sprice.attribute_id = '.$specialPriceId,
                    array('value AS special_price'));
        }
        else
        {
            $collection->getSelect()
                ->joinInner(
                    array('catalog_sprice'	=>	'catalog_product_entity_decimal'),
                    'catalog_sprice.entity_id = e.entity_id AND catalog_sprice.attribute_id = '.$specialPriceId,
                    array('value AS special_price'));;
        }
        /**** END CUSTOM FACTORY X CODE ****/

        $this->setCollection($collection);

        $date_from = $this->_getMysqlFromFormat($this->getFilter('report_from'));
        $date_to = $this->_getMysqlToFormat($this->getFilter('report_to'));

        $this->getCollection()->setDateFilter($date_from, $date_to)->setState();

        $storeIds = $this->getStoreIds();
        if (count($storeIds)) {
            $this->setStoreFilter($storeIds);
        }

        $this->addOrderItems($this->getCustomOption('advancedreports_bestsellers_options_bestsellers_count'));

        $key = $this->getFilter('reload_key');
        if ($key === 'qty') {
            $this->getCollection()->orderByQty();
        }
        elseif ($key === 'total') {
            $this->getCollection()->orderByTotal();
        }
        //echo $this->getCollection()->getSelect();
        $this->_prepareData();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('order', array(
            'header' => Mage::helper('reports')->__('N'),
            'width' => '60px',
            'align' => 'right',
            'index' => 'order',
            'type' => 'number',
            'sortable' => false,
        ));

        $this->addColumn('sku', array(
            'header' => Mage::helper('reports')->__('SKU'),
            'width' => '120px',
            'index' => 'sku',
            'type' => 'text',
            'sortable' => false,
        ));

        /**** START CUSTOM FACTORY X CODE ****/
        $this->addColumn('price',
            array(
                'header'=> Mage::helper('reports')->__('Price'),
                'width' => '120px',
                'type'  => 'price',
                'index' => 'price',
            ));

        $nonsaleFilter = $this->getRequest()->getParam('filter_nonsale');
        if (!$nonsaleFilter)
        {
            $this->addColumn('special_price', array(
                'header' => Mage::helper('reports')->__('Special Price'),
                'width' => '120px',
                'type' => 'price',
                'index' => 'special_price',
            ));
        }
        /**** END CUSTOM FACTORY X CODE ****/

        $this->addColumn('name', array(
            'header' => Mage::helper('reports')->__('Product Name'),
            'index' => 'name',
            'type' => 'text',
            'sortable' => false,
        ));

        $this->addColumn('percent', array(
            'header' => $this->_helper()->__('Percent'),
            'width' => '60px',
            'align' => 'right',
            'index' => 'percent',
            'type' => 'text',
            'sortable' => false,
            'custom_sorting_percent' => 1,
        ));

        $this->addColumn('ordered_qty', array(
            'header' => $this->_helper()->__('Quantity'),
            'width' => '120px',
            'align' => 'right',
            'index' => 'ordered_qty',
            'total' => 'sum',
            'type' => 'number',
            'sortable' => false,
        ));

        $this->addColumn('total', array(
            'header' => Mage::helper('reports')->__('Total'),
            'width' => '120px',
            'type' => 'currency',
            'currency_code' => $this->getCurrentCurrencyCode(),
            'total' => 'sum',
            'index' => 'total',
            'sortable' => false,
        ));

        $this->addColumn('action',
            array(
                'header' => Mage::helper('catalog')->__('Action'),
                'width' => '50px',
                'type' => 'action',
                'align' => 'right',
                'getter' => 'getId',
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
                'index' => 'stores',
            ));

        $this->addExportType('*/*/exportOrderedCsv', $this->_helper()->__('CSV'));
        $this->addExportType('*/*/exportOrderedExcel', $this->_helper()->__('Excel'));

        return $this;
    }

}
