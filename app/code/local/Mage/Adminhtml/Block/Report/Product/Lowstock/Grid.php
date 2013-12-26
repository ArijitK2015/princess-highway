<?php
/**
	add the following columns:
	- product type
	- status
	- season
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
 * Adminhtml low stock products report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Product_Lowstock_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
//    protected $_saveParametersInSession = true;

    public function __construct()
    {
        parent::__construct();
        $this->setId('gridLowstock');
        $this->setUseAjax(false);
    }

    protected function _prepareCollection()
    {
        if ($this->getRequest()->getParam('website')) {
            $storeIds = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } else if ($this->getRequest()->getParam('group')) {
            $storeIds = Mage::app()->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } else if ($this->getRequest()->getParam('store')) {
            $storeId = (int)$this->getRequest()->getParam('store');
        } else {
            $storeId = '';
        }

        /** @var $collection Mage_Reports_Model_Resource_Product_Lowstock_Collection  */
        $collection = Mage::getResourceModel('reports/product_lowstock_collection')
            ->addAttributeToSelect('*')
            ->addAttributeToSelect('season')
            ->setStoreId($storeId)
            ->filterByIsQtyProductTypes()
            ->joinInventoryItem('qty')
            ->useManageStockFilter($storeId)
            ->useNotifyStockQtyFilter($storeId)
            ->setOrder('qty', Varien_Data_Collection::SORT_ORDER_ASC);

        if( $storeId ) {
            $collection->addStoreFilter($storeId);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('season', array(
            'header'    => Mage::helper('reports')->__('Season'),
            'width'     => '120px',            
            'sortable'  => false,
            'index'     => 'season',
            'type'      => 'options',
            'options'   => $this->_getOptions('season')
        ));

        $this->addColumn('sku', array(
            'header'    => Mage::helper('reports')->__('Product SKU'),
            'width'     => '250px',            
            'sortable'  => false,
            'index'     => 'sku'
        ));
        
        $this->addColumn('name', array(
            'header'    => Mage::helper('reports')->__('Product Name'),
            'sortable'  => false,
            'index'     => 'name'
        ));

        $this->addColumn('type',array(
            'header'    => Mage::helper('catalog')->__('Type'),
            'width'     => '120px',
            'index'     => 'type_id',
            'type'      => 'options',
            'options'   => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('catalog')->__('Status'),
            'width'     => '70px',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));

        $this->addColumn('qty', array(
            'header'    =>Mage::helper('reports')->__('Stock Qty'),
            'width'     =>'215px',
            'align'     =>'right',
            'sortable'  =>false,
            'filter'    =>'adminhtml/widget_grid_column_filter_range',
            'index'     =>'qty',
            'type'      =>'number'
        ));

        $this->addExportType('*/*/exportLowstockCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportLowstockExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }

	protected function _getOptions($field) {
	    $attribute = Mage::getResourceModel('eav/entity_attribute_collection')
	        ->setCodeFilter($field)
	        ->getFirstItem();
	    $attrOptions = $attribute->getSource()->getAllOptions(false);
	    $optionsArr = array();
	    foreach ($attrOptions as $option) {
	    	//Mage::log($option['value'] . "=" . $option['label']);
	        $optionsArr[$option['value']] = $option['label'];
	    }
	    return $optionsArr;
	}    
}
