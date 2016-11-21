<?php
/**
 * Region Report Grid
 */
class FactoryX_AWAdvancedreportsMod_Block_Advanced_Region_Grid extends AW_Advancedreports_Block_Advanced_Country_Grid
{
    /**
     * Route to helper options
     * @var string
     */
    protected $_routeOption = FactoryX_AWAdvancedreportsMod_Helper_Data::ROUTE_ADVANCED_REGION;

    /**
     * @return mixed
     */
    public function _helper()
    {
        return Mage::helper('awadvancedreportsmod');
    }

    /**
     * Class constructor
     */
    public function __construct()
    {
        AW_Advancedreports_Block_Advanced_Grid::__construct();
        $this->setId('gridRegion');
    }

    /**
     * Prepare report collection
     * @return AW_Advancedreports_Block_Advanced_Region_Grid
     */
    protected function _prepareCollection()
    {
        AW_Advancedreports_Block_Advanced_Grid::_prepareCollection();

        $this->setCollection(Mage::getResourceModel('awadvancedreportsmod/collection_region'));
        $this->_prepareAbstractCollection();
        $this->getCollection()->addAddress();
        $storeIds = $this->getStoreIds();
        $this->getCollection()->addOrderItemsCount(empty($storeIds));
        $this->getCollection()->setSize(0);
        $this->_helper()->setNeedMainTableAlias(true);
        $this->_prepareData();
        return $this;
    }

    /**
     * Collection
     *
     * @return AW_Advancedreports_Model_Mysql4_Collection_Region
     */
    public function getCollection()
    {
        return $this->_collection;
    }

    /**
     * Retrives Show Chart flag
     * @return boolean
     */
    public function hasRecords()
    {
        return (count($this->_customData))
        && $this->_helper()->getChartParams($this->_routeOption)
        && count($this->_helper()->getChartParams($this->_routeOption));
    }

    /**
     * Add row to custom data
     * @param array $row
     * @return AW_Advancedreports_Block_Advanced_Region_Grid
     */
    protected function _addCustomData($row)
    {
        if (count($this->_customData)) {
            foreach ($this->_customData as &$d) {
                if ($d['region_id'] === $row['region_id']) {
                    $qty = $d['qty_ordered'];
                    $total = $d['total'];
                    unset($d['total']);
                    unset($d['qty_ordered']);
                    $d['total'] = $row['total'] + $total;
                    $d['qty_ordered'] = $row['qty_ordered'] + $qty;
                    return $this;
                }
            }
        }
        $this->_customData[] = $row;
        return $this;
    }

    /**
     * Retrive compare result for two arrays by Total
     * @param array $a
     * @param array $b
     * @return int
     */
    protected function _compareTotalElements($a, $b)
    {
        if ($a['total'] == $b['total']) {
            return 0;
        }
        return ($a['total'] > $b['total']) ? -1 : 1;
    }

    /**
     * Retrive compare result for two arrays by Quantity element
     * @param array $a
     * @param array $b
     * @return int
     */
    protected function _compareQtyElements($a, $b)
    {
        if ($a['qty_ordered'] == $b['qty_ordered']) {
            return 0;
        }
        return ($a['qty_ordered'] > $b['qty_ordered']) ? -1 : 1;
    }

    /**
     * Prepare Custom Data to show chart
     * @return AW_Advancedreports_Block_Advanced_Region_Grid
     */
    protected function _prepareData()
    {
        foreach ($this->getCollection()->getItems() as $order) {
            $row = array();
            foreach ($this->_columns as $column) {
                if (!$column->getIsSystem()) {
                    $row[$column->getIndex()] = $order->getData($column->getIndex());
                }
            }
            if ($order->getRegionId()) {
                $row['region_id'] = $order->getRegionId();
                $row['qty_ordered'] = $order->getSumQty();
                $row['total'] = $order->getSumTotal();
                $this->_addCustomData($row);
            }
        }

        $key = $this->getFilter('reload_key');
        if ($key === 'qty') {
            $this->setDefaultPercentField('qty_ordered');
            //Sort data
            usort($this->_customData, array(&$this, "_compareQtyElements"));

            //All qty
            $qty = 0;
            foreach ($this->_customData as $d) {
                $qty += $d['qty_ordered'];
            }
            foreach ($this->_customData as $i => &$d) {
                $d['order'] = $i + 1;
                $d['percent_data'] = round($d['qty_ordered'] * 100 / $qty);
                //Add title
                $d['region_name'] = Mage::getSingleton('directory/region')->load($d['region_id'])->getName();
            }
        } elseif ($key === 'total') {
            $this->setDefaultPercentField('total');
            //Sort data
            usort($this->_customData, array(&$this, "_compareTotalElements"));

            //All qty
            $total = 0;
            foreach ($this->_customData as $d) {
                $total += $d['total'];
            }
            foreach ($this->_customData as $i => &$d) {
                $d['order'] = $i + 1;
                if ($total != 0) {
                    $d['percent_data'] = round($d['total'] * 100 / $total);
                } else {
                    $d['percent_data'] = 0;
                }

                //Add title
                $d['region_name'] = Mage::getSingleton('directory/region')->load($d['region_id'])->getName();
            }
        } else {
            return $this;
        }
        $this->_preparePage();
        $this->getCollection()->setSize(count($this->_customData));
        Mage::helper('advancedreports')->setChartData($this->_customData, Mage::helper('advancedreports')->getDataKey($this->_routeOption));
        AW_Advancedreports_Block_Advanced_Grid::_prepareData();
        return $this;
    }

    /**
     * Retrives flag to show params selector always
     * @return boolean
     */
    public function getShowAnyway()
    {
        return true;
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
            'type' => 'number'
        ));

        $this->addColumn('region_name', array(
            'header' => Mage::helper('reports')->__('Region'),
            'index' => 'region_name'
        ));

        $this->addColumn('qty_ordered', array(
            'header' => $this->_helper()->__('Quantity'),
            'width' => '120px',
            'align' => 'right',
            'renderer' => 'advancedreports/widget_grid_column_renderer_percent',
            'index'    => 'qty_ordered',
            'total'    => 'sum',
            'type'     => 'number',
        ));

        $this->addColumn('total', array(
            'header' => Mage::helper('reports')->__('Total'),
            'width' => '120px',
            'currency_code' => $this->getCurrentCurrencyCode(),
            'renderer'      => 'advancedreports/widget_grid_column_renderer_percent',
            'index'         => 'total',
            'type'          => 'currency',

        ));

        $this->addExportType('*/*/exportOrderedCsv', $this->_helper()->__('CSV'));
        $this->addExportType('*/*/exportOrderedExcel', $this->_helper()->__('Excel'));

        return $this;
    }

    /**
     * @return mixed
     */
    public function getChartType()
    {
        return AW_Advancedreports_Block_Chart::CHART_TYPE_MAP;
    }
}
