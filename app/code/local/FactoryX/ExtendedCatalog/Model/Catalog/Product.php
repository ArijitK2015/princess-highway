<?php
/**
 *	Add loadParentProductIds function
 *	Source: http://stackoverflow.com/questions/2847164/how-to-find-out-master-product-of-simple-product
 */
class FactoryX_ExtendedCatalog_Model_Catalog_Product extends Mage_Catalog_Model_Product
{
    /**
     * @deprecated after 1.4.2.0
     * @return Mage_Catalog_Model_Product
     */
    public function loadParentProductIds()
    {
        return $this->_getResource()->getParentProductIds($this);
    }
}
