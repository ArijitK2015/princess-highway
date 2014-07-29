<?php
if ((string)Mage::getConfig()->getModuleConfig('MageWorx_SeoSuite')->active == 'true'){
    class FactoryX_ExtendedCatalog_Block_Catalog_Product_View_Product_Abstract extends MageWorx_SeoSuite_Block_Catalog_Product_View {}
} else {
    class FactoryX_ExtendedCatalog_Block_Catalog_Product_View_Product_Abstract extends Mage_Catalog_Block_Product_View {}
}
