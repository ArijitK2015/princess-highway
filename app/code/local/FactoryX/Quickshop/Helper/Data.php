<?php
/**
 * Class FactoryX_Quickshop_Helper_Data
 */
class FactoryX_Quickshop_Helper_Data extends Mage_Core_Helper_Abstract {
    /**
     *
     */
    public function isMagicZoomUsed()
    {
        return Mage::getStoreConfigFlag('quickshop/options/magiczoom');
    }
}