<?php
/** 
	add custom columns to the category product tab grid:
	- status
	- special_price
	- type_id
	- attribute_set_id
	- visibility
	- season
	- colour_description
	- brand
	- news_from_date
	- news_to_date
	- image
	- stock status
	sort by position
**/

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
 * Product in category grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Category_Tab_Product extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('catalog_category_products');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    public function getCategory()
    {
        return Mage::registry('category');
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in category flag
        if ($column->getId() == 'in_category') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
            }
            elseif(!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection()
    {
        if ($this->getCategory()->getId()) {
            $this->setDefaultFilter(array('in_category'=>1));
        }
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('status')
            ->addAttributeToSelect('special_price')            
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('type_id')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('visibility')
            ->addAttributeToSelect('season')
            ->addAttributeToSelect('colour_description')
            ->addAttributeToSelect('brand')
            ->addAttributeToSelect('news_from_date')
            ->addAttributeToSelect('news_to_date')
            ->addAttributeToSelect('image')
            ->addStoreFilter($this->getRequest()->getParam('store'))
            ->joinField('position',
                'catalog/category_product',
                'position',
                'product_id=entity_id',
                'category_id='.(int) $this->getRequest()->getParam('id', 0),
                'left')
			->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left')
			->joinTable( 'cataloginventory/stock_item', 'product_id=entity_id', array("stock_status" => "is_in_stock") );
                
		$collection->setOrder('position');
        $this->setCollection($collection);

        if ($this->getCategory()->getProductsReadonly()) {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
        }

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        if (!$this->getCategory()->getProductsReadonly()) {
            $this->addColumn('in_category', array(
                'header_css_class' => 'a-center',
                'type'      => 'checkbox',
                'name'      => 'in_category',
                'values'    => $this->_getSelectedProducts(),
                'align'     => 'center',
                'index'     => 'entity_id'
            ));
        }
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'entity_id'
        ));
        $this->addColumn('thumbnail', array(
			'header'	=> Mage::helper('catalog')->__('Thumbnail'),
			'width'		=> '90px',
			'index' 	=> 'image',
			'renderer'  => 'Mage_Adminhtml_Block_Catalog_Category_Tab_Renderer_Thumbnail',
			'filter' 	=> false,
			'sortable'	=> false
        ));                
        $this->addColumn('status', array(
            'header'    => Mage::helper('catalog')->__('Status'),
            'width'     => '70px',
            'index'     => 'status',
            'type'  => 'options',
            'options' => Mage::getSingleton('catalog/product_status')->getOptionArray()
        ));
		$this->addColumn('stock_status', array(
            'header'=> 'Availability',
            'width' => '60px',             //this is the column width
            'index' => 'stock_status',
            'type'  => 'options',
            'options' => array('1'=>'In stock','0'=>'Out of stock'),
        ));
		$this->addColumn('news_from_date', array(
            'header'    => Mage::helper('catalog')->__('New From Date'),
            'width'     => '70px',
            'index'     => 'news_from_date',
            'type'		=> 'datetime'
        ));
		$this->addColumn('news_to_date', array(
            'header'    => Mage::helper('catalog')->__('New To Date'),
            'width'     => '70px',
            'index'     => 'news_to_date',
            'type'		=> 'datetime'
        ));
        /*
        $this->addColumn('sku', array(
            'header'    => Mage::helper('catalog')->__('SKU'),
            'width'     => '160px',
            'index'     => 'sku'
        ));
        */     
        $this->addColumn('gender',
            array(
                'header'=> Mage::helper('catalog')->__('Gender'),
                'index' => 'sku',
                'type'  => 'options',
                'options' => array('b' => "Boys", 'g' => "Girls", 'n' => "Nonspecific"),
                'renderer' => 'Mage_Adminhtml_Block_Catalog_Product_Renderer_Gender',
                'filter_condition_callback' => array($this, "_filterGenderCondition")
        ));        
        $this->addColumn('name', array(
            'header'    => Mage::helper('catalog')->__('Name'),
            'index'     => 'name'
        ));
        $this->addColumn('type',
            array(
                'header'=> Mage::helper('catalog')->__('Type'),
                'width' => '60px',
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
        
        $this->addColumn('visibility',
            array(
                'header'=> Mage::helper('catalog')->__('Visibility'),
                'width' => '70px',
                'index' => 'visibility',
                'type'  => 'options',
                'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
        ));
        

		$season_options = array();
		$brand_options = array();
		$colour_options = array();
		$attributes = Mage::getModel('eav/entity_attribute_option')->getCollection()->setStoreFilter()->join('attribute','attribute.attribute_id=main_table.attribute_id', 'attribute_code');
        foreach ($attributes as $attribute) {
            if ($attribute->getAttributeCode() == 'season') {
                $season_options[$attribute->getOptionId()] = $attribute->getValue();
			}        	
            if ($attribute->getAttributeCode() == 'brand') {
                $brand_options[$attribute->getOptionId()] = $attribute->getValue();
			}
			if ($attribute->getAttributeCode() == 'colour_description') {
				$colour_options[$attribute->getOptionId()] = $attribute->getValue();	
			}
		}
		/*
 		$this->addColumn('season',
            array(
                'header'=> Mage::helper('catalog')->__('Season'),
                'width' => '80px',
                'index' => 'season',
                'type'  => 'options',
                'options' => $season_options
        ));
		*/
 		$this->addColumn('brand',
            array(
                'header'=> Mage::helper('catalog')->__('Brand'),
                'width' => '80px',
                'index' => 'brand',
                'type'  => 'options',
                'options' => $brand_options
        ));

		/*
 		$this->addColumn('colour_description',
            array(
                'header'=> Mage::helper('catalog')->__('Colour'),
                'width' => '80px',
                'index' => 'colour_description',
                'type'  => 'options',
                'options' => $colour_options
        ));        
        */
        
        $this->addColumn('special_price', array(
            'header'    => Mage::helper('catalog')->__('Sale Price'),
            'type'  => 'currency',
            'width'     => '1',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index'     => 'special_price'
        ));

        $this->addColumn('price', array(
            'header'    => Mage::helper('catalog')->__('Price'),
            'type'  => 'currency',
            'width'     => '1',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index'     => 'price'
        ));
        
        $editable = true;
        if ($this->getCategory()->getProductsReadonly()) {
        	$editable = false;
        }

        // override
        $editable = true;
        $this->addColumn('position', array(
            'header'    => "Position", // Mage::helper('catalog')->__('Position')
            'width'     => '50px',
            'type'      => 'number',
            'index'     => 'position',
            'sortable'  => true,
            'editable'  => $editable,
            'renderer'  => 'adminhtml/widget_grid_column_renderer_input'
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('selected_products');
        if (is_null($products)) {
            $products = $this->getCategory()->getProductsPosition();
            return array_keys($products);
        }
        return $products;
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


}

