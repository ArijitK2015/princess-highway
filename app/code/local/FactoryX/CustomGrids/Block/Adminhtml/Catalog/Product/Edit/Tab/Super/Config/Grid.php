<?php
/**
 *	Add colour to associated products grid
 */
class FactoryX_CustomGrids_Block_Adminhtml_Catalog_Product_Edit_Tab_Super_Config_Grid extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid
{
	private static $ATTR_COLOUR = "colour";
	
    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid
     */
    protected function _prepareCollection()
    {
        $allowProductTypes = array();
        foreach (Mage::helper('catalog/product_configuration')->getConfigurableAllowedTypes() as $type) {
            $allowProductTypes[] = $type->getName();
        }

        $product = $this->_getProduct();
        $collection = $product->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->addAttributeToSelect('price')
            ->addFieldToFilter('attribute_set_id',$product->getAttributeSetId())
            ->addFieldToFilter('type_id', $allowProductTypes)
            ->addFilterByRequiredOptions()
            ->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner');

		// Add colour
		$collection->joinAttribute(self::$ATTR_COLOUR, 'catalog_product/' . self::$ATTR_COLOUR, 'entity_id', null, 'inner');

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            Mage::getModel('cataloginventory/stock_item')->addCatalogInventoryToProductCollection($collection);
        }

        foreach ($product->getTypeInstance(true)->getUsedProductAttributes($product) as $attribute) {
            $collection->addAttributeToSelect($attribute->getAttributeCode());
            $collection->addAttributeToFilter($attribute->getAttributeCode(), array('notnull'=>1));
        }

        $this->setCollection($collection);

        if ($this->isReadonly()) {
            $collection->addFieldToFilter('entity_id', array('in' => $this->_getSelectedProducts()));
        }

        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $product = $this->_getProduct();
        $attributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);

        if (!$this->isReadonly()) {
            $this->addColumn('in_products', array(
                'header_css_class' => 'a-center',
                'type'      => 'checkbox',
                'name'      => 'in_products',
                'values'    => $this->_getSelectedProducts(),
                'align'     => 'center',
                'index'     => 'entity_id',
                'renderer'  => 'adminhtml/catalog_product_edit_tab_super_config_grid_renderer_checkbox',
                'attributes' => $attributes
            ));
        }

        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('catalog')->__('Name'),
            'index'     => 'name'
        ));


        $sets = Mage::getModel('eav/entity_attribute_set')->getCollection()
            ->setEntityTypeFilter($this->_getProduct()->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '130px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
        ));

        $this->addColumn('sku', array(
            'header'    => Mage::helper('catalog')->__('SKU'),
            'width'     => '80px',
            'index'     => 'sku'
        ));

        $this->addColumn('price', array(
            'header'    => Mage::helper('catalog')->__('Price'),
            'type'      => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index'     => 'price'
        ));

        $this->addColumn('is_saleable', array(
            'header'    => Mage::helper('catalog')->__('Inventory'),
            'renderer'  => 'adminhtml/catalog_product_edit_tab_super_config_grid_renderer_inventory',
            'filter'    => 'adminhtml/catalog_product_edit_tab_super_config_grid_filter_inventory',
            'index'     => 'is_saleable'
        ));

 		$this->addColumn(self::$ATTR_COLOUR,
            array(
                'header'=> Mage::helper('catalog')->__('Colour'),
                'width' => '120px',
                'index' => self::$ATTR_COLOUR,
                'filter_index' => self::$ATTR_COLOUR,
                'type'  => 'options',
                'options' => $this->_getColourOptions()
        ));

        foreach ($attributes as $attribute) {
            $productAttribute = $attribute->getProductAttribute();
            $productAttribute->getSource();
            $this->addColumn($productAttribute->getAttributeCode(), array(
                'header'    => $productAttribute->getFrontend()->getLabel(),
                'index'     => $productAttribute->getAttributeCode(),
                'type'      => $productAttribute->getSourceModel() ? 'options' : 'number',
                'options'   => $productAttribute->getSourceModel() ? $this->getOptions($attribute) : ''
            ));
        }

         $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Edit'),
                        'url'     => $this->getEditParamsForAssociated(),
                        'field'   => 'id',
                        'onclick'  => 'superProduct.createPopup(this.href);return false;'
                    )
                ),
                'filter'    => false,
                'sortable'  => false
        ));

        return parent::_prepareColumns();
    }
	
	protected function _getColourOptions() 
	{
	    $colour = Mage::getResourceModel('eav/entity_attribute_collection')
	        ->setCodeFilter(self::$ATTR_COLOUR)
	        ->getFirstItem();
			
	    $colourOptions = $colour->getSource()->getAllOptions(false);
		
	    $optionsArr = array();
	    foreach ($colourOptions as $option) 
		{
	        $optionsArr[$option['value']] = $option['label'];
	    }
		
	    return $optionsArr;
	}
}
