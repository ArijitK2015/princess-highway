<?php

/**
 * Class FactoryX_HtmlInject_Helper_Data
 */
class FactoryX_HtmlInject_Helper_Data extends Mage_Core_Helper_Abstract {
    
    private static $defaultPaddingMethod = 'centered'; // centered | justified
    private static $defaultMaxWidth = 980;

    /**
     * @return bool
     */
    public function adjustNav()
    {
        return Mage::getStoreConfigFlag('htmlinject/navigation/enabled');
    }

    /**
     * @return int|mixed
     */
    public function getMaxWidth()
    {
        return (
            Mage::getStoreConfig('htmlinject/navigation/maxwidth')
            ?
            Mage::getStoreConfig('htmlinject/navigation/maxwidth')
            :
            self::$defaultMaxWidth
        );
    }
    

    /**
     * @return int|mixed
     */
    public function getPaddingMethod()
    {
        return (
            Mage::getStoreConfig('htmlinject/navigation/padding_method')
            ?
            Mage::getStoreConfig('htmlinject/navigation/padding_method')
            :
            self::$defaultPaddingMethod
        );
    }
}