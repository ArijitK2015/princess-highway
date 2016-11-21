<?php
/**
 *	Fix the configurable export product bug
 * 	Source: http://www.razoyo.com/blog/2013/01/25/magento-1-7-configurable-product-export-fix/
 *  Only affects Magento < 1.8.1.0 and Magento > 1.5.1.0
 *	Official fix from Magento 2: https://github.com/magento/magento2/blob/master/app/code/Magento/ConfigurableProduct/Model/Resource/Product/Type/Configurable.php
 */
class FactoryX_BugFixes_Model_Catalog_Resource_Product_Type_Configurable extends Mage_Catalog_Model_Resource_Product_Type_Configurable
{
    /**
     * Collect product options with values according to the product instance and attributes, that were received
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $attributes
     * @return array
     */
    public function getConfigurableOptions($product, $attributes)
    {
        if (version_compare(Mage::getVersion(),"1.5.1.0","<=") || version_compare(Mage::getVersion(),"1.8.1.0",">="))
        {
            return parent::getConfigurableOptions($product, $attributes);
        }
        else {
            $attributesOptionsData = array();
            foreach ($attributes as $superAttribute) {
                $select = $this->_getReadAdapter()->select()
                    ->from(
                        array(
                            'super_attribute' => $this->getTable('catalog/product_super_attribute')
                        ),
                        array(
                            'sku' => 'entity.sku',
                            'product_id' => 'super_attribute.product_id',
                            'attribute_code' => 'attribute.attribute_code',
                            'option_title' => 'option_value.value',
                            'pricing_value' => 'attribute_pricing.pricing_value',
                            'pricing_is_percent' => 'attribute_pricing.is_percent'
                        )
                    )->joinInner(
                        array(
                            'product_link' => $this->getTable('catalog/product_super_link')
                        ),
                        'product_link.parent_id = super_attribute.product_id',
                        array()
                    )->joinInner(
                        array(
                            'attribute' => $this->getTable('eav/attribute')
                        ),
                        'attribute.attribute_id = super_attribute.attribute_id',
                        array()
                    )->joinInner(
                        array(
                            'entity' => $this->getTable('catalog/product')
                        ),
                        'entity.entity_id = product_link.product_id',
                        array()
                    )->joinInner(
                        array(
                            'entity_value' => $superAttribute->getBackendTable()
                        ),
                        implode(
                            ' AND ',
                            array(
                                $this->_getReadAdapter()
                                    ->quoteInto('entity_value.entity_type_id = ?', $product->getEntityTypeId()),
                                'entity_value.attribute_id = super_attribute.attribute_id',
                                'entity_value.store_id = 0',
                                'entity_value.entity_id = product_link.product_id'
                            )
                        ),
                        array()
                    )->joinLeft(
                        array(
                            'option_value' => $this->getTable('eav/attribute_option_value')
                        ),
                        implode(' AND ', array(
                            'option_value.option_id = entity_value.value',
                            'option_value.store_id = ' . Mage_Core_Model_App::ADMIN_STORE_ID,
                        )),
                        array()
                    )->joinLeft(
                        array(
                            'attribute_pricing' => $this->getTable('catalog/product_super_attribute_pricing')
                        ),
                        implode(' AND ', array(
                            'super_attribute.product_super_attribute_id = attribute_pricing.product_super_attribute_id',
                            'entity_value.value = attribute_pricing.value_index'
                        )),
                        array()
                    )->where('super_attribute.product_id = ?', $product->getId());

                $attributesOptionsData[$superAttribute->getAttributeId()] = $this->_getReadAdapter()->fetchAll($select);
            }
            return $attributesOptionsData;
        }
    }
}
