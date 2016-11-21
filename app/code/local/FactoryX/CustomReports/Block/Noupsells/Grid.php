<?php
/**
 * Class FactoryX_CustomReports_Block_Noupsells_Grid
 *
 */

class FactoryX_CustomReports_Block_Noupsells_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    protected $filterArray = array();

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('noupsellsReportGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('desc');

        // TODO: these should come from settings
        $this->additionalAttributes = array(
            "online_only"
        );
    }

    /**
     * @param $args
     */
    public function fillArray($args)
    {
        $product_with_upsell_id = $args['row']['product_id'];

        if (!in_array($product_with_upsell_id, $this->filterArray))
        {
            array_push($this->filterArray, $product_with_upsell_id);
        }
    }

    /**
     * @return array
     */
    public function getFilterUpsellsItems()
    {
        $upsellsItemsCollection = Mage::getModel('catalog/product_link')->useUpSellLinks()
            ->getLinkCollection()
            ->addFieldToSelect('product_id');

        // Call iterator walk method with collection query string and callback method as parameters
        // Has to be used to handle massive collection instead of foreach
        Mage::getSingleton('core/resource_iterator')->walk($upsellsItemsCollection->getSelect(), array(array($this, 'fillArray')));

        return $this->filterArray;
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        /*
        // old method, doesnt allow for for having and group by
        $collection = Mage::getResourceModel('catalog/product_collection')
                        ->addAttributeToSelect('entity_id')
                        ->addAttributeToSelect('sku')
                        ->addAttributeToSelect('name')
                        ->addAttributeToSelect('status')
                        ->addAttributeToSelect('visibility')
                        ->addAttributeToFilter('type_id', array('eq' => 'configurable'))
                        ->addAttributeToFilter('entity_id',array('nin'=>$this->getFilterUpsellsItems()));
        if (!empty($this->filterArray)) {
            $collection->addAttributeToFilter('entity_id',array('nin'=>$this->getFilterUpsellsItems()));
        }
        */

        $collection = Mage::getResourceModel('reports/product_collection');
        // set type so the correct getSelectCountSql() logic is used
        $collection->setSelectCountSqlType(FactoryX_CustomReports_Model_Reports_Resource_Product_Collection::SELECT_COUNT_SQL_TYPE_CUSTOM_REPORT_UPSELLS);

        //select attribute_id from eav_attribute where entity_type_id = 4 and attribute_code like 'name'
        //select attribute_id from eav_attribute where attribute_code like 'status'
        //select attribute_id from eav_attribute where attribute_code like 'visibility'

        $attIdN = Mage::getModel('catalog/product')->getResource()->getAttribute("name")->getId();
        $attIdS = Mage::getModel('catalog/product')->getResource()->getAttribute("status")->getId();
        $attIdV = Mage::getModel('catalog/product')->getResource()->getAttribute("visibility")->getId();

        foreach($this->additionalAttributes as $attribute) {
            // check if the attibute exists
            if ($att = Mage::getModel('catalog/product')->getResource()->getAttribute($attribute)) {
                $attId = $att->getId();
                Mage::log(sprintf("%s->%s|%s", __METHOD__, $attId, $att->getBackendType()));
                $collection->getSelect()->joinInner(
                    array($attribute => sprintf("catalog_product_entity_%s", $att->getBackendType()) ),
                    sprintf("%s.entity_id = e.entity_id AND %s.attribute_id = %d AND %s.store_id = 0",
                        $attribute, $attribute, $attId, $attribute),
                    array(sprintf("value as %s", $attribute))
                );
            }
        }

        $collection->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns('entity_id', 'e')
            ->columns('sku', 'e')
            ->columns('type_id', 'e')
            ->joinLeft(
                array('cpl' => 'catalog_product_link'),
                'e.entity_id = cpl.product_id AND cpl.link_type_id = 4',
                array('count(cpl.linked_product_id) as upsell_count')
            )
            ->joinInner(
                array('name' => 'catalog_product_entity_varchar'),
                sprintf("name.entity_id = e.entity_id AND name.attribute_id = %d AND name.store_id = 0", $attIdN),
                array('value as name')
            )
            ->joinInner(
                array('status' => 'catalog_product_entity_int'),
                sprintf("status.entity_id = e.entity_id AND status.attribute_id = %d AND status.store_id = 0", $attIdS),
                array('value as status')
            )
            ->joinInner(
                array('visibility' => 'catalog_product_entity_int'),
                sprintf("visibility.entity_id = e.entity_id AND visibility.attribute_id = %d AND visibility.store_id = 0", $attIdV),
                array('value as visibility')
            )
            ->where(sprintf("visibility.value = %d", Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH))
            ->where('type_id in (\'simple\', \'configurable\')')
            ->group('e.sku')
            ->having(sprintf("count(linked_product_id) < %d", $this->_getMinUpsellsCount()));

        $session = Mage::getSingleton('core/session');
        $sortOrder = 'name ASC';
        if ($session->getNoupsellsSort() && $session->getNoupsellsDir()) {
            //Mage::helper('customreports')->log(sprintf("session: %s\n", print_r($session->getData(), true)));
            $sortOrder = sprintf("%s %s", $session->getNoupsellsSort(), strtoupper($session->getNoupsellsDir()));
        }
        $collection->getSelect()->order($sortOrder);
        //Mage::helper('customreports')->log(sprintf("sortOrder: %s\n", $sortOrder));
        Mage::helper('customreports')->log(sprintf("sql: %s\n", $collection->getSelect()->__toString()));

        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    /**
     * get minUpsellsCount
     * @return $minUpsellsCount
     */
    private function _getMinUpsellsCount() {
        $minUpsellsCount = FactoryX_CustomReports_Block_Noupsells::DEFAULT_MIN_UPSELLS;
        $session = Mage::getSingleton('core/session');
        if ($session->getMinNoupsellsCount()) {
            //Mage::helper('customreports')->log(sprintf("%s->getData:%s", __METHOD__, print_r($session->getData(), true)) );
            if (is_numeric($session->getMinNoupsellsCount())) {
                //Mage::helper('customreports')->log(sprintf("%s->is_numeric:%s", __METHOD__, $session->getMinNoupsellsCount()) );
                $minUpsellsCount = $session->getMinNoupsellsCount();
            }
        }
        //Mage::helper('customreports')->log(sprintf("%s->minUpsellsCount:%s", __METHOD__, $minUpsellsCount));
        return $minUpsellsCount;
    }

    /**
     * name is an eav attribute which uses addAttributeToFilter for filtering this
     * will add a new join to the select statement causing a duplicate column error
     */
    public function filterCallbackName($collection, $column) {
        $field = $column->getFilterIndex() ? $column->getFilterIndex() : $column->getIndex();
        $value = $column->getFilter()->getValue();
        $collection->getSelect()->where("$field like ?", '%' . $value . '%');
    }

    public function filterCallback($collection, $column) {
        $field = $column->getFilterIndex() ? $column->getFilterIndex() : $column->getIndex();
        $value = $column->getFilter()->getValue();
        $collection->getSelect()->where("$field = ?", $value);
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'     => Mage::helper('reports')->__('Product ID'),
            'width'      => '50px',
            'index'      => 'entity_id'
        ));

        $this->addColumn('name', array(
            'header'        => Mage::helper('reports')->__('Product Name'),
            'width'         => '50px',
            'index'         => 'name',
            'filter_index'  => 'name.value',
            'filter_condition_callback' => array($this, 'filterCallbackName')
        ));

        $this->addColumn('sku', array(
            'header'        => Mage::helper('reports')->__('Sku'),
            'width'         => '50px',
            'index'         => 'sku',
            'filter_index'  => 'sku'
        ));

        $this->addColumn('upsell_count', array(
            'header'        => Mage::helper('reports')->__('Upsells'),
            'width'         => '50px',
            'index'         => 'upsell_count',
            //'filter'    => false,
            //'sortable'  => false
        ));

        foreach($this->additionalAttributes as $code) {
            $attribute = Mage::getResourceModel('catalog/eav_attribute')->loadByCode('catalog_product', $code);
            if (!$attribute->getId()) {
                continue;
            }
            $label = $attribute->getStoreLabel();
            //$label = ucwords(preg_replace("/_/", " ", $code));

            $this->addColumn($code, array(
                'header'        => Mage::helper('reports')->__($label),
                'width'         => '50px',
                'index'         => $code,
                // TODO: make dynamic based on attribute
                'type'          => 'options',
                'options'       => array(
                    0   => "No",
                    1   => "Yes"
                ),
                'filter_index'  => sprintf("%s.value", $code),
                'filter_condition_callback' => array($this, 'filterCallback')
                //'filter'    => false,
                //'sortable'  => false
            ));
        }

        $this->addColumn('visibility', array(
            'header'        => Mage::helper('catalog')->__('Visibility'),
            'width'         => '70px',
            'index'         => 'visibility',
            'type'          => 'options',
            'options'       => Mage::getModel('catalog/product_visibility')->getOptionArray(),
            'filter'        => false,
            'sortable'      => false
        ));

        $this->addColumn('status', array(
            'header'        => Mage::helper('catalog')->__('Status'),
            'width'         => '70px',
            'index'         => 'status',
            'type'          => 'options',
            'options'       => Mage::getSingleton('catalog/product_status')->getOptionArray(),
            'filter_index'  => 'status.value',
            'filter_condition_callback' => array($this, 'filterCallback')
        ));

        $this->addColumn('action', array(
            'header'        =>  Mage::helper('reports')->__('Action'),
            'width'         => '100px',
            'type'          => 'action',
            'getter'        => 'getId',
            'actions'       => array(
                array(
                    'caption'   => Mage::helper('reports')->__('Edit Product'),
                    'url'       => array(
                        'base'      => 'adminhtml/catalog_product/edit',
                        'params'    => array('active_tab' => 'upsell')
                    ),
                    'target'    => '_blank',
                    'rel'       => 'noopener noreferrer',
                    'field'     => 'id'
                )
            ),
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportNoupsellsCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportNoupsellsExcel', Mage::helper('reports')->__('Excel'));

        return parent::_prepareColumns();
    }

}