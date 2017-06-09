<?php

/**
 * Class FactoryX_Pinterest_Block_Header
 */
class FactoryX_Pinterest_Block_Header extends Mage_Core_Block_Template{

    const CACHE_TAG	= 'factoryx_pinterest';

    protected $_product;

    protected function _construct(){
        if (Mage::getStoreConfig('pinterest/general/enable')){
            $this->setData('isActive',true);
            $this->_product = Mage::registry('current_product');
            $this->setData('price',number_format($this->_product->getPrice(),2));
            $this->setData('url',$this->_product->getProductUrl());
            $this->setData('description',$this->_product->getName());
            $this->setData('media',$this->helper('catalog/image')->init($this->_product, 'image')->__toString());
        }else{
            $this->setData('isActive',false);
        }
        $this->addData(array(
            'cache_lifetime' => 3600,
            'cache_tags'     => array(FactoryX_Pinterest_Block_Header::CACHE_TAG),
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
        $productId = Mage::registry('current_product')->getId();
        $cacheKey  = sprintf("%s_%d_%s_%s", $className, Mage::app()->getStore()->getId(), Mage::getSingleton('core/design_package')->getPackageName(), $productId);
        return $cacheKey;
    }
}