<?php
/**
 * Product short description block
 *
 * @category   Mage
 * @package    FactoryX_ExtendedCatalog
 * @author
 */
class FactoryX_ExtendedCatalog_Block_Catalog_Product_View_Shortdescription extends Mage_Core_Block_Template {
    protected $_product = null;

    function getProduct() {
        if (!$this->_product) {
            $this->_product = Mage::registry('product');
        }
        return $this->_product;
    }
}
