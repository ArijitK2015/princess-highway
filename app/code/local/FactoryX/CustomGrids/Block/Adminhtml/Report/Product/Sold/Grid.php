<?php
/*
 *	Add sku, season, colour, brand, size, special price and price to the products ordered report grid
 */
class FactoryX_CustomGrids_Block_Adminhtml_Report_Product_Sold_Grid extends Mage_Adminhtml_Block_Report_Product_Sold_Grid
{
    /**
     * Prepare Grid columns
     *
     * @return Mage_Adminhtml_Block_Report_Product_Sold_Grid
     */
    protected function _prepareColumns()
    {
		$this->addColumn('sku', array(
            'header'    => Mage::helper('reports')->__('sku'),
            'index'     =>'order_items_sku'
        ));     	
    	    	
        $this->addColumn('season', array(
            'header'    => Mage::helper('reports')->__('Season'),
            'index'     => 'season',
            'type'      => 'options',
            'options' 	=> $this->_getAttributeOptions('season')
        ));
		
		$this->addColumn('brand', array(
            'header'    => Mage::helper('reports')->__('Brand'),
            'index'     => 'brand',
            'type'      => 'options',
            'options' 	=> $this->_getAttributeOptions('brand')
        ));

        $this->addColumn('colour', array(
            'header'    => Mage::helper('reports')->__('Colour'),
            'index'     => 'colour',
            'type'      => 'options',
            'options' 	=> $this->_getAttributeOptions('colour'),
            'renderer'  => 'FactoryX_CustomGrids_Block_Adminhtml_Report_Product_Sold_Renderer_Colour'
        ));

        $this->addColumn('size', array(
            'header'    => Mage::helper('reports')->__('Size'),
            'index'     => 'size',
            'renderer'  => 'FactoryX_CustomGrids_Block_Adminhtml_Report_Product_Sold_Renderer_Size'
        ));

        $this->addColumn('name', array(
            'header'    =>Mage::helper('reports')->__('Product Name'),
            'index'     =>'order_items_name'
        ));

        $this->addColumn('special_price', array(
            'header'    =>Mage::helper('reports')->__('Sale Price'),
            'width'     =>'120px',
            'type'      =>'currency',
            'currency_code' => $this->getCurrentCurrencyCode(),
            'index'     =>'special_price'
        ));

        $this->addColumn('price', array(
            'header'    =>Mage::helper('reports')->__('Price'),
            'width'     =>'120px',
            'type'      =>'currency',
            'currency_code' => $this->getCurrentCurrencyCode(),
            'index'     =>'price'
        ));

        $this->addColumn('ordered_qty', array(
            'header'    =>Mage::helper('reports')->__('Quantity Ordered'),
            'width'     =>'120px',
            'align'     =>'right',
            'index'     =>'ordered_qty',
            'total'     =>'sum',
            'type'      =>'number'
        ));

        $this->addExportType('*/*/exportSoldCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportSoldExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }
    
    public function _getAttributeOptions($attrId) 
	{
	    $options = Mage::getResourceModel('eav/entity_attribute_collection')
	        ->setCodeFilter($attrId)
	        ->getFirstItem();
			
	    $optionsOptions = $options->getSource()->getAllOptions(false);
		
	    $optionsArr = array();
	    foreach ($optionsOptions as $option) 
		{
	        $optionsArr[$option['value']] = $option['label'];
	    }
		
	    return $optionsArr;
		
    }
}
