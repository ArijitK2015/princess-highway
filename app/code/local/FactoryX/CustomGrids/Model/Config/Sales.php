<?php

/**
 * Class FactoryX_CustomGrids_Model_Config_Sales
 */
class FactoryX_CustomGrids_Model_Config_Sales extends FactoryX_CustomGrids_Model_Config
{
    protected $_tableAlias = array(
        'sales_flat_order'          =>  'sfo',
        'sales_flat_order_address'  =>  'sfoa',
        'sales_flat_order_payment'  =>  'sfop'
    );

    protected $_ambiguousColumns = array(
        'created_at',
        'increment_id',
        'base_grand_total',
        'grand_total',
        'status'
    );

    protected $_collection;

    /**
     * Get the table alias
     * @param $table
     * @return bool
     */
    public function getTableAlias($table) {
        $tableAlias = false;
        if (array_key_exists($table,$this->_tableAlias)) {
            $tableAlias = $this->_tableAlias[$table];
        }
        return $tableAlias;
    }

    /**
     * Get the table relation
     * @param $table
     * @return bool
     */
    public function getTableRelation($entity,$table)
    {
        $tableRelation = false;
        if (
            array_key_exists($entity, $this->_tableRelations)
            &&
            array_key_exists($table, $this->_tableRelations[$entity])
        ) {
            $tableRelation = $this->_tableRelations[$entity][$table];
        }
        return $tableRelation;
    }

    /**
     * Get the attribute collection
     * @param $block
     * @return array
     */
    public function getAttributeCollection($block,$alphaSort = false)
    {
        $relatedEntityType = $this->getRelatedEntityType($block);
        $collection = $this->getFlatCollection($relatedEntityType,$block,$alphaSort);
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
        // Entity type
        $entityType = $this->getRelatedEntityType($blockType);
        // Entity config
        $entityConfig = Mage::getSingleton('customgrids/config_'.$entityType);
        // Get the flat attributes
        $attributes = $entityConfig->getFlatCollection($entityType,$blockType);

        foreach($attributes as $attribute)
        {
            if ($attribute->getAttributeCode() == $column->getAttributeCode())
            {
                $label = $attribute->getFrontendLabel();
                $config = array(
                    'header'  => $label,
                    'index' => $column->getAttributeCode(),
                );
                if ($attribute->getConfig())
                {
                    $config = array_merge($config,$attribute->getConfig());
                }
                switch ($column->getAttributeCode())
                {
                    case 'created_by':
                        $config['options'] = FactoryX_CustomGrids_Block_Adminhtml_Renderer_AdminUser::getUsers();
                        break;
                    case 'state':
                        $config['options'] = Mage::getSingleton('sales/order_config')->getStates();
                        break;
                    case 'customer_group_id':
                        $config['options'] = FactoryX_CustomGrids_Block_Adminhtml_Renderer_CustomerGroup::getCustomerGroupsArray();
                        break;
                    case 'method':
                        $config['options'] = Mage::helper('payment')->getPaymentMethodList(true);
                        break;
                    case 'country_id':
                        $config['options'] = Mage::helper('customgrids')->countryToOptionHash();
                        $config['type'] = 'options';
                        break;
                    default:
                        break;
                }
                break;
            }
        }

        return $config;
    }

    /**
     * @return array
     */
    public function getAmbiguousColumns()
    {
        return $this->_ambiguousColumns;
    }

    /**
     * Get a collection of flat attributes
     * @param $type
     * @return Varien_Data_Collection
     * @throws Exception
     */
    public function getFlatCollection($type,$block,$alphaSort = false)
    {
        $collection = new Varien_Data_Collection();

        $defaultColumns = $this->getDefaultColumns($block);

        foreach ($this->_flatAttributes[$type] as $key => $attributes)
        {
            foreach ($attributes as $attribute)
            {
                if (!in_array($attribute['code'],$defaultColumns))
                {
                    $obj = new Varien_Object();
                    $obj->setAttributeCode($attribute['code']);
                    $obj->setFrontendLabel($attribute['label']);
                    if (array_key_exists('config',$attribute))
                    {
                        $obj->setConfig($attribute['config']);
                    }
                    $collection->addItem($obj);
                }
            }
        }

        if ($alphaSort)
        {
            $array = (array)$collection->getIterator();
            usort($array, array($this,'_usort'));
            $collection = $array;
        }

        return $collection;
    }

    /**
     * Callback for sorting items
     * Currently supports only sorting by one column
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    protected function _usort($a, $b)
    {
        return strcmp($a->getFrontendLabel(),$b->getFrontendLabel());
    }

    /**
     * Get the flat attributes of an entity type
     * @param $type
     * @return bool
     */
    public function getFlatAttributes($type)
    {
        $flatAttributes = false;
        if (array_key_exists($type, $this->_flatAttributes)) {
            $flatAttributes = $this->_flatAttributes[$type];
        }
        return $flatAttributes;
    }
}