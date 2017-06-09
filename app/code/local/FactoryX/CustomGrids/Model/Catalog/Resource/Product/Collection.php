<?php
/**
 * Class FactoryX_CustomGrids_Model_Catalog_Resource_Product_Collection
 * Handle org price and special price ordering
 */
if ((string)Mage::getConfig()->getModuleConfig('FactoryX_BugFixes')->active == 'true') {
    class FactoryX_CustomGrids_Model_Catalog_Resource_Product_Collection extends FactoryX_BugFixes_Model_Catalog_Resource_Product_Collection
    {
        public function setOrder($attribute, $dir = 'desc')
        {
            if ($attribute == 'price' || $attribute == "special_price" || $attribute == "org_price") {
                $this->addAttributeToSort($attribute, $dir);
            } else {
                parent::setOrder($attribute, $dir);
            }
            return $this;
        }
    }
}
else {
    class FactoryX_CustomGrids_Model_Catalog_Resource_Product_Collection extends Mage_Catalog_Model_Resource_Product_Collection
    {
        public function setOrder($attribute, $dir = 'desc')
        {
            if ($attribute == 'price' || $attribute == "special_price" || $attribute == "org_price") {
                $this->addAttributeToSort($attribute, $dir);
            } else {
                parent::setOrder($attribute, $dir);
            }
            return $this;
        }
    }
}
