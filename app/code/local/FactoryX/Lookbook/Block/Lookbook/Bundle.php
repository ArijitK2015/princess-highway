<?php
/**
 *	That is the frontend block for the lookbook bundle overview
 */
class FactoryX_Lookbook_Block_Lookbook_Bundle extends Mage_Core_Block_Template
{

    /**
     *	Retrieve the current bundle for the frontend
     * @return Mage_Catalog_Model_Product
     */
    public function getCurrentBundle() {
            $bundleId = $this->getRequest()->getParam('id');

            $bundle = Mage::getModel('catalog/product')->load($bundleId);

            return $bundle;
    }

}