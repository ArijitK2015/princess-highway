<?php

/**
 * Class FactoryX_DisableLog_Helper_Data
 */
class FactoryX_DisableLog_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @return mixed
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag('disablelog/options/enable');
    }

}