<?php
/**
 * Change label position to new and adds new sorting labels
 */


class FactoryX_ExtendedCatalog_Model_Catalog_Config extends Mage_Catalog_Model_Config
{
    /**
     * Retrieve Attributes Used for Sort by as array
     * key = code, value = name
     *
     * @return array
     */
    public function getAttributeUsedForSortByArray()
    {
        $options = array(
            'position'  => Mage::helper('catalog')->__('New'),
        );
        foreach ($this->getAttributesUsedForSortBy() as $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute_Abstract */
            $options[$attribute->getAttributeCode()] = $attribute->getStoreLabel();
        }

        return $options;
    }
}
