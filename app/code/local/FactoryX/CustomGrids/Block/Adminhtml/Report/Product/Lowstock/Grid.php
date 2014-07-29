<?php
/*
 *	Add product type, status and season to the lowstock report grid
 */
class FactoryX_CustomGrids_Block_Adminhtml_Report_Product_Lowstock_Grid extends Mage_Adminhtml_Block_Report_Product_Lowstock_Grid
{

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
            'header'    => Mage::helper('reports')->__('Stock Qty'),
            'width'     => '215px',
            'align'     => 'right',
            'sortable'  => false,
            'filter'    => 'adminhtml/widget_grid_column_filter_range',
            'index'     => 'qty',
            'type'      => 'number'
        ));

        $this->addExportType('*/*/exportLowstockCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportLowstockExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }

	protected function _getOptions($field) 
	{
	    $attribute = Mage::getResourceModel('eav/entity_attribute_collection')
	        ->setCodeFilter($field)
	        ->getFirstItem();
			
	    $attrOptions = $attribute->getSource()->getAllOptions(false);
		
	    $optionsArr = array();
	    foreach ($attrOptions as $option) 
		{
	        $optionsArr[$option['value']] = $option['label'];
	    }
		
	    return $optionsArr;
	}    
}
