<?php
/**
 * Who:  Alvin Nguyen
 * When: 13/10/2014
 * Why:  
 */ 
class FactoryX_CacheSupport_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * @return mixed
     */
    public function getRecentlyViewedEnable(){
        return Mage::getStoreConfigFlag('varnishcache/recentlyviewed/enable');
    }

    /**
     * @return mixed
     */
    public function getRecentlyViewedTtl(){
        return Mage::getStoreConfig('varnishcache/recentlyviewed/ttl');
    }

    /**
     * @return mixed
     */
    public function getRecentlyViewedCount(){
        return Mage::getStoreConfig('varnishcache/recentlyviewed/count');
    }

    /**
     * @return mixed
     */
    public function getTopLinksEnable(){
        return Mage::getStoreConfigFlag('varnishcache/toplinks/enable');
    }

    /**
     * @return mixed
     */
    public function getMobileCartEnable(){
        return Mage::getStoreConfigFlag('varnishcache/mobilecart/enable');
    }

    public function isDebugMode(){
        return Mage::getStoreConfigFlag('varnishcache/cachewarming/debug');
    }

    public function enableWarmCMS(){
        return Mage::getStoreConfigFlag('varnishcache/cachewarming/cmspage');
    }

    public function enableWarmCategory(){
        return Mage::getStoreConfigFlag('varnishcache/cachewarming/catpage');
    }

    public function enableWarmProduct(){
        return Mage::getStoreConfigFlag('varnishcache/cachewarming/prodpage');
    }

    public function getWarmThreadNo(){
        return Mage::getStoreConfig('varnishcache/cachewarming/threads');
    }

    public function getProductTop(){
        return Mage::getStoreConfig('varnishcache/cachewarming/prodpage_topx');
    }

    public function getProxy(){
        return Mage::getStoreConfig('varnishcache/cachewarming/proxy');
    }
}