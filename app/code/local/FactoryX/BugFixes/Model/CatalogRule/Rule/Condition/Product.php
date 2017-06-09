<?php
/**
 * Fix a bug where catalog rule were not applied correctly (only affecting 1.8.0.0)
 * Class FactoryX_BugFixes_Model_CatalogRule_Rule_Condition_Product
 */

class FactoryX_BugFixes_Model_CatalogRule_Rule_Condition_Product extends Mage_CatalogRule_Model_Rule_Condition_Product
{
	 /**
     * Get attribute value
     *
     * @param Varien_Object $object
     * @return mixed
     */
    protected function _getAttributeValue($object)
    {
        if (version_compare(Mage::getVersion(),"1.8.0.0","=="))
        {
            $storeId = $object->getStoreId();
            $defaultStoreId = Mage_Core_Model_App::ADMIN_STORE_ID;
            $productValues = isset($this->_entityAttributeValues[$object->getId()]) ? $this->_entityAttributeValues[$object->getId()] : array($defaultStoreId => $object->getData($this->getAttribute()));
            $defaultValue = isset($productValues[$defaultStoreId]) ? $productValues[$defaultStoreId] : null;
            $value = isset($productValues[$storeId]) ? $productValues[$storeId] : $defaultValue;
            $value = $this->_prepareDatetimeValue($value, $object);
            $value = $this->_prepareMultiselectValue($value, $object);
            return $value;
        }
        else return parent::_getAttributeValue($object);
    }
}