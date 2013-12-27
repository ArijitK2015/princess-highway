<?php
/**
	add custom columns:
	- type_id
	- season
	- online_only
	- in_store_only
	- colour_description
	- brand
	- org_price
	display in_store and online only for managers and admins only
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
 * Adminhtml customer grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('productGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('product_filter');

    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->addAttributeToSelect('season')
            ->addAttributeToSelect('online_only')
            ->addAttributeToSelect('in_store_only')            
            ->addAttributeToSelect('colour_description')
            ->addAttributeToSelect('brand');
	
	//$collection->joinAttribute('colour_description', 'catalog_product/colour_description', 'entity_id', null, 'inner', $store->getId());
	//$collection->joinAttribute('brand', 'catalog_product/brand', 'entity_id', null, 'inner', $store->getId());

	//Mage::log("sql=" . $collection->getSelect());
        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }
        if ($store->getId()) {
            //$collection->setStoreId($store->getId());
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->addStoreFilter($store);
            $collection->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner', $adminStore);
            $collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
            $collection->joinAttribute('special_price', 'catalog_product/special_price', 'entity_id', null, 'left', $store->getId());
            $collection->joinAttribute('org_price', 'catalog_product/org_price', 'entity_id', null, 'left', $store->getId());
        }
        else {
            $collection->addAttributeToSelect('price');
            $collection->addAttributeToSelect('special_price');
            $collection->addAttributeToSelect('org_price');
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        }

        $this->setCollection($collection);

        parent::_prepareCollection();
        
        //$this->getCollection()->addWebsiteNamesToResult();

        return $this;
    }

	/**
	$column Mage_Adminhtml_Block_Widget_Grid_Column
	*/
    protected function _addColumnFilterToCollection($column) {
    	//Mage::log(sprintf("%s->column=%s", __METHOD__, $column->getId()));
        if ($this->getCollection()) {	  
        	if ($column->getId() == 'gender') {
        		// column->getFilter()	Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
        		$cond = $column->getFilter()->getCondition();
        	    //Mage::log(sprintf("gender=%s", $cond['eq']));
        	}
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField('websites',
                    'catalog/product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left');
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }
	
    protected function _prepareColumns() {

    	$userId = Mage::getSingleton('admin/session')->getUser()->getUserId();
    	$role = Mage::getModel('admin/user')->load($userId)->getRole()->getData();
    	$isManager = preg_match("/manager/i", $role['role_name']);
    	$isAdmin = preg_match("/admin/i", $role['role_name']);
        
        $this->addColumn('entity_id',
            array(
                'header'=> Mage::helper('catalog')->__('ID'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'entity_id',
        ));
        $this->addColumn('name',
            array(
                'header'=> Mage::helper('catalog')->__('Name'),
                'index' => 'name',
        ));

        $store = $this->_getStore();
        if ($store->getId()) {
            $this->addColumn('custom_name',
                array(
                    'header'=> Mage::helper('catalog')->__('Name in %s', $store->getName()),
                    'index' => 'custom_name',
            ));
        }

        $this->addColumn('type',
            array(
                'header'=> Mage::helper('catalog')->__('Type'),
                'width' => '120px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '200px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
        ));

        $this->addColumn('sku',
            array(
                'header'=> Mage::helper('catalog')->__('SKU'),
                'width' => '200px',
                'index' => 'sku',
        ));

 		$this->addColumn('season',
            array(
                'header'=> Mage::helper('catalog')->__('Season'),
                'index' => 'season',
                'filter_index' => 'season',
                'type'  => 'options',
                'options' => $this->_getOptions('season')
        ));

        $this->addColumn('gender',
            array(
                'header'=> Mage::helper('catalog')->__('Gender'),
                'index' => 'sku',
                'type'  => 'options',
                'options' => array('b' => "Boys", 'g' => "Girls", 'n' => "Nonspecific"),
                'renderer' => 'Mage_Adminhtml_Block_Catalog_Product_Renderer_Gender',
                'filter_condition_callback' => array($this, '_filterGenderCondition')
        ));

 		$this->addColumn('brand',
            array(
                'header'=> Mage::helper('catalog')->__('Brand'),
                'width' => '80px',
                'index' => 'brand',
                'filter_index' => 'brand',
                'type'  => 'options',
                'options' => $this->_getOptions('brand'),
                //'renderer' => 'Mage_Adminhtml_Block_Catalog_Product_Renderer_Brand',                
				//'filter_condition_callback' => array($this, '_filterBrandCondition')
        ));

 		$this->addColumn('colour_description',
            array(
                'header'=> Mage::helper('catalog')->__('Colour'),
                'width' => '120px',
                'index' => 'colour_description',
                'filter_index' => 'colour_description',
                'type'  => 'options',
                'options' => $this->_getOptions('colour_description')
        ));
        
        $store = $this->_getStore();
        $this->addColumn('price',
            array(
                'header'=> Mage::helper('catalog')->__('Price'),
                'width' => '120px',
                'type'  => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'price',
        ));

        $this->addColumn('special_price',
            array(
                'header'=> Mage::helper('catalog')->__('Sale Price'),
                'width' => '120px',
                'align'	=> 'right',
                'type'  => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'special_price',
                //'renderer' => 'Mage_Adminhtml_Block_Catalog_Product_Renderer_Price',
        ));

        $this->addColumn('org_price',
            array(
                'header'=> Mage::helper('catalog')->__('Previous Sale Price'),
                'width' => '120px',
                'align'	=> 'right',
                'type'  => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'org_price',
                //'renderer' => 'Mage_Adminhtml_Block_Catalog_Product_Renderer_Price',
        ));
        
        if ($isManager || $isAdmin) {
     		$this->addColumn('online_only',
                array(
                    'header'=> Mage::helper('catalog')->__('Online Only'),
                    'width' => '100px',
                    'index' => 'online_only',
                    'filter_index' => 'online_only',
                    'type'  => 'options',
                    'options'   => array(1 => 'Yes', 0 => 'No')
            ));
            $this->addColumn('in_store_only',
                array(
                    'header'=> Mage::helper('catalog')->__('In-Store Only'),
                    'width' => '100px',
                    'index' => 'in_store_only',                
                    'type'  => 'options',
                    'options'   => array(1 => 'Yes', 0 => 'No')
                    //'options' => Mage::getModel('catalog/yesy_no')->getOptionArray(),
            ));
        }        

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $this->addColumn('qty',
                array(
                    'header'=> Mage::helper('catalog')->__('Qty'),
                    'width' => '100px',
                    'type'  => 'number',
                    'index' => 'qty',
            ));
        }

        $this->addColumn('visibility',
            array(
                'header'=> Mage::helper('catalog')->__('Visibility'),
                'width' => '70px',
                'index' => 'visibility',
                'type'  => 'options',
                'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
        ));

        $this->addColumn('status',
            array(
                'header'=> Mage::helper('catalog')->__('Status'),
                'width' => '70px',
                'index' => 'status',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));
		
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('websites',
                array(
                    'header'=> Mage::helper('catalog')->__('Websites'),
                    'width' => '100px',
                    'sortable'  => false,
                    'index'     => 'websites',
                    'type'      => 'options',
                    'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
            ));
        }
				
        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Edit'),
                        'url'     => array(
                            'base'=>'*/*/edit',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
        ));

        if (Mage::helper('catalog')->isModuleEnabled('Mage_Rss')) {
            $this->addRssList('rss/catalog/notifystock', Mage::helper('catalog')->__('Notify Low Stock RSS'));
        }

        return parent::_prepareColumns();
    }

	protected function _getOptions($field) {
	    $brand = Mage::getResourceModel('eav/entity_attribute_collection')
	        ->setCodeFilter($field)
	        ->getFirstItem();
	    $brandOptions = $brand->getSource()->getAllOptions(false);
	    $optionsArr = array();
	    foreach ($brandOptions as $option) {
	    	//Mage::log($option['value'] . "=" . $option['label']);
	        $optionsArr[$option['value']] = $option['label'];
	    }
	    return $optionsArr;
	} 

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('product');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> Mage::helper('catalog')->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete'),
             'confirm' => Mage::helper('catalog')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('catalog/product_status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('catalog')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('catalog')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));

        if (Mage::getSingleton('admin/session')->isAllowed('catalog/update_attributes')){
            $this->getMassactionBlock()->addItem('attributes', array(
                'label' => Mage::helper('catalog')->__('Update Attributes'),
                'url'   => $this->getUrl('*/catalog_product_action_attribute/edit', array('_current'=>true))
            ));
        }

        $this->getMassactionBlock()->addItem('export', array(
                'label' => Mage::helper('catalog')->__('Export to CSV'),
                'url'   => $this->getUrl('*/*/massExport', array('_current'=>true)),
            ));

        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'store'=>$this->getRequest()->getParam('store'),
            'id'=>$row->getId())
        );
    }

	/**
	create a filter based on the second char of the sku being g for girls and b for boys
	*/
	protected function _filterGenderCondition($collection, $column) {
    	if (!$value = $column->getFilter()->getValue()) {
        	return;
    	}
		// does NOT support regexp $this->getCollection()->addFieldToFilter
		if ($value == "n") {
			$regexp = "^[a-zA-Z]{1}[^bBgG]{1}";
		}
		else {
			$regexp = sprintf("^[a-zA-Z]{1}[%s|%s]", $value, strtoupper($value));
		}
		$sqlCondition = sprintf("sku REGEXP '%s'", $regexp);
		$this->getCollection()->getSelect()->where($sqlCondition);
	}    


/*
	protected function _filterBrandCondition($collection, $column) {
		Mage::log(__METHOD__);
    	if (!$value = $column->getFilter()->getValue()) {
        	return;
    	}
		$this->getCollection()->addFieldToFilter('brand', array('finset' => $value));
	}    
*/
}
