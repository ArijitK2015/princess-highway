<?php

/**
 * Class FactoryX_CustomGrids_Model_Config
 */
class FactoryX_CustomGrids_Model_Config
{
    protected $_allowedBlockTypes = array(
        'Catalog Product Grid'                          =>  'Mage_Adminhtml_Block_Catalog_Product_Grid',
        'Catalog Category Product Tab Grid'             =>  'Mage_Adminhtml_Block_Catalog_Category_Tab_Product',
        'Catalog Product Associated Products Tab Grid'  =>  'Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid',
        'Report Lowstock Product Grid'                  =>  'Mage_Adminhtml_Block_Report_Product_Lowstock_Grid',
        'Report Product Ordered Grid'                   =>  'Mage_Adminhtml_Block_Report_Product_Sold_Grid',
        'Sales Order Grid'                              =>  'Mage_Adminhtml_Block_Sales_Order_Grid',
        'Sales Invoice Grid'                            =>  'Mage_Adminhtml_Block_Sales_Invoice_Grid',
        'Sales Shipment Grid'                           =>  'Mage_Adminhtml_Block_Sales_Shipment_Grid'
    );

    protected $_relatedEntityType = array(
        'Mage_Adminhtml_Block_Catalog_Product_Grid'                         =>  Mage_Catalog_Model_Product::ENTITY,
        'Mage_Adminhtml_Block_Catalog_Category_Tab_Product'                 =>  Mage_Catalog_Model_Product::ENTITY,
        'Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid'   =>  Mage_Catalog_Model_Product::ENTITY,
        'Mage_Adminhtml_Block_Report_Product_Lowstock_Grid'                 =>  Mage_Catalog_Model_Product::ENTITY,
        'Mage_Adminhtml_Block_Report_Product_Sold_Grid'                     =>  Mage_Catalog_Model_Product::ENTITY,
        'Mage_Adminhtml_Block_Sales_Order_Grid'                             =>  Mage_Sales_Model_Order::ENTITY,
        'Mage_Adminhtml_Block_Sales_Invoice_Grid'                           =>  Mage_Sales_Model_Order_Invoice::HISTORY_ENTITY_NAME,
        'Mage_Adminhtml_Block_Sales_Shipment_Grid'                          =>  Mage_Sales_Model_Order_Shipment::HISTORY_ENTITY_NAME
    );

    protected $_relatedCollectionType = array(
        'Mage_Adminhtml_Block_Catalog_Product_Grid'                         =>  'Mage_Catalog_Model_Resource_Product_Collection',
        'Mage_Adminhtml_Block_Catalog_Category_Tab_Product'                 =>  'Mage_Catalog_Model_Resource_Product_Collection',
        'Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid'   =>  'Mage_Catalog_Model_Resource_Product_Collection',
        'Mage_Adminhtml_Block_Report_Product_Lowstock_Grid'                 =>  'Mage_Reports_Model_Resource_Product_Lowstock_Collection',
        'Mage_Adminhtml_Block_Report_Product_Sold_Grid'                     =>  'Mage_Reports_Model_Resource_Product_Sold_Collection',
        'Mage_Adminhtml_Block_Sales_Order_Grid'                             =>  'Mage_Sales_Model_Resource_Order_Collection',
        'Mage_Adminhtml_Block_Sales_Invoice_Grid'                           =>  'Mage_Sales_Model_Resource_Order_Invoice_Grid_Collection',
        'Mage_Adminhtml_Block_Sales_Shipment_Grid'                          =>  'Mage_Sales_Model_Resource_Order_Shipment_Grid_Collection'

    );

    protected $_defaultColumns = array(
        'Mage_Adminhtml_Block_Catalog_Product_Grid' =>  array(
            'entity_id',
            'name',
            'type_id',
            'attribute_set_id',
            'sku',
            'price',
            'qty',
            'visibility',
            'status'
        ),
        'Mage_Adminhtml_Block_Catalog_Category_Tab_Product' =>  array(
            'entity_id',
            'name',
            'sku',
            'price'
        ),
        'Mage_Adminhtml_Block_Report_Product_Lowstock_Grid' =>  array(
            'name',
            'sku',
            'qty'
        ),
        'Mage_Adminhtml_Block_Report_Product_Sold_Grid' =>  array(
            'name',
            'ordered_qty'
        ),
        'Mage_Adminhtml_Block_Sales_Order_Grid' =>  array(
            'real_order_id',
            'created_at',
            'billing_name',
            'shipping_name',
            'base_grand_total',
            'grand_total',
            'status'
        ),
        'Mage_Adminhtml_Block_Sales_Invoice_Grid' =>  array(
            'increment_id',
            'created_at',
            'order_increment_id',
            'order_created_at',
            'billing_name',
            'state',
            'grand_total'
        ),
        'Mage_Adminhtml_Block_Sales_Shipment_Grid' =>  array(
            'increment_id',
            'created_at',
            'order_increment_id',
            'order_created_at',
            'shipping_name',
            'total_qty'
        ),
        'Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid' => array(
            'entity_id',
            'name',
            'set_name',
            'sku',
            'price',
            'is_saleable'
        )
    );

    /**
     * Get the allowed block types as a to option array
     * @return array
     */
    public function getAllowedBlocksToOptionArray()
    {
        $array = array();
        $allowedBlockTypes = $this->getAllowedBlockTypes();
        foreach ($allowedBlockTypes as $blockLabel => $blockType)
        {
            $array[] = array('value' => $blockType, 'label' => $blockLabel);
        }
        return $array;
    }

    /**
     * Get the allowed block types
     * @return array
     */
    public function getAllowedBlockTypes()
    {
        return $this->_allowedBlockTypes;
    }

    /**
     * Get the related entity type of a block
     * @param $block
     * @return mixed
     */
    public function getRelatedEntityType($block)
    {
        if (array_key_exists($block,$this->_relatedEntityType))
            return $this->_relatedEntityType[$block];
        else return false;
    }

    /**
     * Transform an attribute collection in a to option array
     * @param $collection
     * @return array
     */
    protected function _toOptionArray($collection,$alphaSort = false)
    {
        $attributeArray = array();
        if ($alphaSort && $collection instanceof Varien_Data_Collection_Db)
        {
            $collection->setOrder('frontend_label',$collection::SORT_ORDER_ASC);
        }
        foreach($collection as $a)
        {
            $attributeArray[$a->getAttributeCode()] = $a->getFrontendLabel() . " (" . $a->getAttributeCode() . ")";
        }
        return $attributeArray;
    }

    /**
     * Get a collection related entity type
     * @param $blockType
     * @return bool
     */
    public function getCollectionType($blockType)
    {
        if (array_key_exists($blockType,$this->_relatedCollectionType))
            return $this->_relatedCollectionType[$blockType];
        else return false;
    }

    /**
     * Get the flat attributes of an entity type
     * @param $type
     * @return bool
     */
    public function getDefaultColumns($type)
    {
        if (array_key_exists($type,$this->_defaultColumns))
        {
            $array = array();

            // Remove the removed columns
            foreach ($this->_defaultColumns[$type] as $defaultCol)
            {
                $removedCol = Mage::getResourceModel('customgrids/column_collection')->addFieldToFilter('grid_block_type',$type)->addFieldToFilter('remove',1)->addFieldToFilter('attribute_code',$defaultCol);
                if ($removedCol->getSize())
                    continue;

                $array[] = $defaultCol;
            }

            return $array;
        }
        else return false;
    }

    /**
     * Get the after columns
     * @param $type
     * @return array
     */
    public function getAfterColumns($type)
    {
        $array = array();
        // Default column
        $defaultColumns = $this->getDefaultColumns($type);

        // Current editing column
        $model = Mage::registry('current_model');
        if ($model)
        {
            $data = $model->getData();
        }

        // Exclude the already used after columns
        $afterColumnsUsed = Mage::getResourceModel('customgrids/column_collection')->addFieldToSelect('after_column')->addFieldToFilter('grid_block_type',$type);

        if (isset($data) && $data && array_key_exists('column_id',$data))
        {
            $afterColumnsUsed->addFieldToFilter('column_id',array('neq' => $data['column_id']));
        }

        // Columns to filter
        $toFilter = array();

        foreach ($afterColumnsUsed as $afterColumnUsed)
        {
            $toFilter[] = $afterColumnUsed->getAfterColumn();
        }

        if ($defaultColumns)
        {
            foreach ($defaultColumns as $defaultColumn)
            {
                if (!in_array($defaultColumn,$toFilter))
                {
                    $array[] = array('value' => $defaultColumn, 'label' => $defaultColumn);
                }
            }
        }
        // Existing columns
        $existingColumns = Mage::getResourceModel('customgrids/column_collection')->addFieldToSelect('attribute_code')->addFieldToFilter('grid_block_type',$type);

        // Exclude the current column
        if (isset($data)
            && $data
            && array_key_exists('attribute_code',$data)
            && array_key_exists('grid_block_type',$data)
            && $data['grid_block_type'] == $type)
        {
            $existingColumns->addFieldToFilter('attribute_code',array('neq'=>$data['attribute_code']));
        }

        foreach ($existingColumns as $existingColumn)
        {
            $array[] = array('value' => $existingColumn->getAttributeCode(), 'label' => $existingColumn->getAttributeCode());
        }
        return $array;
    }

    public function getDefaultCollection($block)
    {
        $array = array();
        if (array_key_exists($block,$this->_defaultColumns))
        {
            foreach ($this->_defaultColumns[$block] as $column)
            {
                $array[$column] = $column;
            }
        }
        return $array;
    }
}