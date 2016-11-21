<?php

/**
 * Class FactoryX_ImageCdn_Block_Footer
 */
class FactoryX_ImageCdn_Block_Footer extends Mage_Core_Block_Template{
    protected function _construct()
    {
        $this->addData(array(
            'cache_lifetime' => 86400,
            'cache_tags'     => array("imagecdn_footer"),
            'cache_key'      => $this->makeCacheKey(get_class($this))
        ));
    }

    /**
     * @return mixed
     */
    public function getCacheKey()
    {
        if (!$this->hasData('cache_key')) {
            $cacheKey = $this->makeCacheKey(get_class($this));
            $this->setCacheKey($cacheKey);
        }
        return $this->getData('cache_key');
    }

    /**
     * @param $className
     * @return string
     */
    protected function makeCacheKey($className)
    {
        $cacheKey  = sprintf("%s_%d_%s", $className, Mage::app()->getStore()->getId(), Mage::getSingleton('core/design_package')->getPackageName());
        return $cacheKey;
    }
}