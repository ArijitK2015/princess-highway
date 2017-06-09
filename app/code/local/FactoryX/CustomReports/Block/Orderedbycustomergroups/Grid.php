<?php

/**
 * Class FactoryX_CustomReports_Block_Orderedbycustomergroups_Grid
 */
class FactoryX_CustomReports_Block_Orderedbycustomergroups_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    protected $arrayCollection = [];

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('orderedbycustomergroupsReportGrid');
    }

    /**
     * @param $args
     */
    public function fillCollection($args)
    {
        // Get Sku and Name
        $sku = array_key_exists('sku',$args['row']) ? $args['row']['sku'] : $args['row']['catalog_product.sku'];
        $name = array_key_exists('name',$args['row']) ? $args['row']['name'] : $args['row']['order_items_name'];
        $price = array_key_exists('price',$args['row']) ? $args['row']['price'] : '';
        $specialPrice = array_key_exists('special_price',$args['row']) ? $args['row']['special_price'] : '';

        // If the sku is not set
        if (!$sku)
        {
            // We get the sku
            $collection = Mage::getResourceModel('catalog/product_collection')
                ->addFieldToFilter('entity_id', [$args['row']['entity_id']])
                ->addAttributeToSelect(['sku'])
                ->setPageSize(1);

            $sku = $collection->getFirstItem()->getSku();

            // If there's still no sku
            if (!$sku)
            {
                // That means the product has been deleted
                $sku = "UNKNOWN";
            }
        }
        // If the name is not set
        if (!$name)
        {
            // We get the name
            $collection = Mage::getResourceModel('catalog/product_collection')
                ->addFieldToFilter('entity_id', [$args['row']['entity_id']])
                ->addAttributeToSelect(['name'])
                ->setPageSize(1);

            $name = $collection->getFirstItem()->getName();

            // If there's still no name
            if (!$name)
            {
                // That means the product has been deleted
                $name = "PRODUCT NO LONGER EXISTS";
            }
        }

        // We fill the array with the data
        $this->arrayCollection[$args['row']['entity_id']."_".$args['row']['customer_group_id']] = [
            'sku'            =>    $sku,
            'name'            =>    $name,
            'price'             =>  $price,
            'special_price'             =>  $specialPrice,
            'ordered_qty'    =>    $args['row']['ordered_qty'],
            'customer_group_id'    =>    $args['row']['customer_group_id'],
            'product_id'    =>    $args['row']['entity_id']
        ];
    }

    /**
     * @return $this
     * @throws Exception
     * @throws Zend_Date_Exception
     */
    protected function _prepareCollection()
    {

        // Get the session
        $session = Mage::getSingleton('core/session');

        // Dates for one week
        $store = Mage_Core_Model_App::ADMIN_STORE_ID;
        $timezone = Mage::app()->getStore($store)->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);
        date_default_timezone_set($timezone);

        // Automatic -30 days if no dates provided
        if ($session->getFrom())
        {
            $sDate = $session->getFrom();
        }
        else
        {
            $sDate = date('Y-m-d 00:00:00',
                Mage::getModel('core/date')->timestamp(strtotime('-30 days'))
            );
        }
        if ($session->getTo())
        {
            $eDate = $session->getTo();
        }
        else
        {
            $eDate = date('Y-m-d 23:59:59',
                Mage::getModel('core/date')->timestamp(time())
            );
        }

        ###############################################################################

        $start = new Zend_Date($sDate);
        $start->setTimeZone("UTC");

        $end = new Zend_Date($eDate);
        $end->setTimeZone("UTC");

        ###############################################################################

        $from = $start->toString("Y-MM-dd HH:mm:ss");
        $to = $end->toString("Y-MM-dd HH:mm:ss");


        // Get the products with their ordered quantity
        $collection = Mage::getResourceModel('reports/product_collection')
            ->addAttributeToSelect('*')
            ->addOrderedQty($from, $to)
            ->joinAttribute(
                'price',
                'catalog_product/price',
                'entity_id',
                null,
                'left'
            )
            ->joinAttribute(
                'special_price',
                'catalog_product/price',
                'entity_id',
                null,
                'left'
            )
            ->setOrder('ordered_qty');

        $collection->getSelect()->reset(Zend_Db_Select::GROUP);

        // Get the from part to be able to modify the left join
        $part = $collection->getSelect()->getPart(Zend_Db_Select::FROM);

        // Composity types
        $compositeTypeIds = Mage::getSingleton('catalog/product_type')->getCompositeTypes();

        // Connection
        $connection = $collection->getConnection();

        // Remove the condition that forces non composite types
        $oldCond = $connection->quoteInto('(e.type_id NOT IN (?)) AND', $compositeTypeIds);
        $part['e']['joinCondition'] = str_replace($oldCond, '', $part['e']['joinCondition']);

        // Set the new from part
        $collection->getSelect()->setPart(Zend_Db_Select::FROM, $part);

        $collection->getSelect()->columns('order.customer_group_id');
        $collection->getSelect()->group(array('e.sku','order.customer_group_id'));

        $collection->printlogquery(false, true);

        // Call iterator walk method with collection query string and callback method as parameters
        // Has to be used to handle massive collection instead of foreach
        Mage::getSingleton('core/resource_iterator')->walk($collection->getSelect(), array(array($this, 'fillCollection')));

        // Convert the array to a collection
        $finalCollection = new FactoryX_CustomReports_Model_Report();
        foreach($this->arrayCollection as $entry){
            $rowObj = new Varien_Object();
            $rowObj->setData($entry);
            $finalCollection->addItem($rowObj);
        }
        $finalCollection->setOrder('ordered_qty');

        $this->setCollection($finalCollection);

        parent::_prepareCollection();

        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('sku', array(
            'header'    => Mage::helper('reports')->__('Product SKU'),
            'width'     => '50',
            'index'     => 'sku'
        ));

        $customerGroups = Mage::getResourceModel('customer/group_collection');
        $custOptions = [];
        foreach ($customerGroups as $customerGroup) {
            $custOptions[$customerGroup->getCustomerGroupId()] = $customerGroup->getCustomerGroupCode();
        }

        $this->addColumn('customer_group_id', array(
            'header'    => Mage::helper('reports')->__('Customer Group'),
            'width'     => '50',
            'index'     => 'customer_group_id',
            'type'      => 'options',
            'options'   => $custOptions
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('reports')->__('Product Name'),
            'width'     => '300',
            'index'     => 'name'
        ));

        $this->addColumn('price', array(
            'header'    => Mage::helper('reports')->__('Product Price'),
            'width'     => '300',
            'index'     => 'price'
        ));

        $this->addColumn('special_price', array(
            'header'    => Mage::helper('reports')->__('Product Special Price'),
            'width'     => '300',
            'index'     => 'special_price'
        ));

        $this->addColumn('ordered_qty', array(
            'header'    => Mage::helper('reports')->__('Ordered Quantity'),
            'width'     => '150',
            'index'     => 'ordered_qty',
        ));

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('reports')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getProductId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('reports')->__('Edit Product'),
                        'url'       => array('base'=> 'adminhtml/catalog_product/edit/'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
            ));

        $this->addExportType('*/*/exportOrderedbycustomergroupsCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportOrderedbycustomergroupsExcel', Mage::helper('reports')->__('Excel'));

        return parent::_prepareColumns();
    }

}