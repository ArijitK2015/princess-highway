<?php

/**
 * Class FactoryX_CustomGrids_Model_Config_Catalog_Product
 */
class FactoryX_CustomGrids_Model_Config_Catalog_Product extends FactoryX_CustomGrids_Model_Config
{
    protected $_attributesToAdd = array(
        Mage_Catalog_Model_Product::ENTITY => array(
            'Product Type' => 'type_id',
            'Creation Date' => 'created_at',
            'Update Date'    => 'updated_at',
            'Attribute Set Name'    => 'attribute_set_id'
        )
    );

    /**
     * Get the attribute collection
     * @param $block
     * @return array
     */
    public function getAttributeCollection($block,$alphaSort = false)
    {
        $relatedEntityType = $this->getRelatedEntityType($block);

        $collection = Mage::getModel('eav/config')->getEntityType($relatedEntityType)->getAttributeCollection()->addFieldToFilter('is_visible',1)->addFieldToFilter('attribute_code',array('nin' => $this->getDefaultColumns($block)));

        // Add extra attributes which are not stored under the eav_attribute table
        $extraAttr = array_key_exists($relatedEntityType,$this->_attributesToAdd) ? $this->_attributesToAdd[$relatedEntityType] : array();
        foreach($extraAttr as $key => $value)
        {
            $obj = new Varien_Object();
            $obj->setAttributeCode($value);
            $obj->setFrontendLabel($key);
            $collection->addItem($obj);
        }

        return $this->_toOptionArray($collection,$alphaSort);
    }

    /**
     * Get the config of the column
     * @param $column
     * @param $blockType
     * @return array
     */
    public function getConfig($column,$blockType)
    {
        // Get the attribute
        $attribute = Mage::getSingleton('eav/config')->getAttribute($this->getRelatedEntityType($column->getGridBlockType()), $column->getAttributeCode());

        // Corresponding label
        $label = $attribute->getFrontend()->getLabel();
        // Corresponding input type
        $inputType = $attribute->getFrontendInput();

        // Default config for every type
        $config = array(
            'header'=> $label,
            'index' => $column->getAttributeCode(),
            'filter_index' => $column->getAttributeCode()
        );

        // Specific config depending on the input type
        switch ($inputType)
        {
            case 'select':
            case 'boolean':
                // Options selector for both select and boolean types
                $config['type'] = 'options';
                $config['options'] = Mage::helper('customgrids')->_getOptions($attribute);
                break;
            case 'price':
                $config = array_merge($config,$this->_getPriceConfigByBlock($blockType));
                break;
            case 'date':
                // Date selector
                $config['type'] = 'datetime';
                break;
            case 'media_image':
                // Image renderer not filterable not sortable
                $config['renderer'] = 'customgrids/adminhtml_renderer_thumbnail';
                $config['filter'] = false;
                $config['sortable'] = false;
                break;
            case 'multiselect':
                /* Not implemented yet */
            case 'weee':
                /* Not implemented yet */
            case 'textarea':
            case 'text':
            default:
                break;
        }

        // Custom attribute config
        $config = array_merge($config,$this->_getStaticAttrConfig($column->getAttributeCode()));

        return $config;
    }

    /**
     * Price config changes depending on the block type
     * @param $blockType
     * @return array
     */
    protected function _getPriceConfigByBlock($blockType)
    {
        $array = array();

        switch($blockType)
        {
            case 'Mage_Adminhtml_Block_Catalog_Product_Grid':
                // Price selector and currency
                $store = Mage::app()->getStore((int) Mage::app()->getRequest()->getParam('store', 0));
                $array['type'] = 'price';
                $array['currency_code'] = $store->getBaseCurrency()->getCode();
                break;
            case 'Mage_Adminhtml_Block_Catalog_Category_Tab_Product':
                $array['type'] = 'currency';
                $array['currency_code'] = (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);
                break;
            default:
                break;
        }

        return $array;
    }

    /**
     * Some attribute are not real attributes and need to be configured on the fly
     * @param $attrCode
     * @return array
     */
    protected function _getStaticAttrConfig($attrCode)
    {
        $array = array();

        switch ($attrCode)
        {
            case 'type_id':
                $array['type']  = 'options';
                $array['options'] = Mage::getSingleton('catalog/product_type')->getOptionArray();
                $array['header'] = 'Product Type';
                break;
            case 'created_at':
                $array['type'] = 'datetime';
                $array['header'] = 'Product Creation Date';
                break;
            case 'updated_at':
                $array['type'] = 'datetime';
                $array['header'] = 'Product Update Date';
                break;
            case 'attribute_set_id':
                $array['type']  = "options";
                $array['options'] = Mage::getResourceModel('eav/entity_attribute_set_collection')
                    ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
                    ->load()
                    ->toOptionHash();
                $array['header'] = 'Attribute Set Name';
                break;
            default:
                break;
        }

        return $array;
    }
}