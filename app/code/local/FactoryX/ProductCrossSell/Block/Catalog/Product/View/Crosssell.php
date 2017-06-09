<?php
/**
 *
 * see Mage_Checkout_Block_Cart_Crosssell
 */
class FactoryX_ProductCrossSell_Block_Catalog_Product_View_Crosssell extends Mage_Catalog_Block_Product_Abstract {


    protected $_items;

    protected $_itemCollection;

    public function _construct()
    {
        $this->addData(array(
            'cache_lifetime' => 3600,
            'cache_tags'     => $this->getCacheTags(),
            'cache_key'      => $this->getCacheKey(),
        ));
    }

    protected function _prepareData()
    {
        $product = Mage::registry('product');
        //Mage::helper('productcrosssell')->log(sprintf("%s->product: %s", __METHOD__, $product->getId()) );
        /* @var $product Mage_Catalog_Model_Product */
        $attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
        //Mage::helper('productcrosssell')->log(sprintf("%s->attributes: %s", __METHOD__, print_r(array_keys($attributes), true)) );

        $this->_itemCollection = $product->getCrossSellProductCollection()
            ->addAttributeToSelect($attributes)
            ->setPositionOrder()
            ->addStoreFilter();

        //Mage::helper('productcrosssell')->log(sprintf("%s->crosssells: %s", __METHOD__, count($this->_itemCollection)) );

        if (Mage::helper('catalog')->isModuleEnabled('Mage_Checkout')) {
            Mage::getResourceSingleton('checkout/cart')->addExcludeProductFilter($this->_itemCollection,
                Mage::getSingleton('checkout/session')->getQuoteId()
            );
            $this->_addProductAttributesAndPrices($this->_itemCollection);
        }
        // Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($this->_itemCollection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_itemCollection);

        // call iterator walk method with collection query string and callback method as parameters
        //Mage::getSingleton('core/resource_iterator')->walk($this->_itemCollection->getSelect(), array(array($this, 'collectionCallback')));

        $customLabel = Mage::helper('productcrosssell')->getCustomImageUpsells();
        //Mage::helper('productcrosssell')->log(sprintf("%s->customLabel: %s", __METHOD__, $customLabel) );
        Mage::helper('productcrosssell')->addCustomImage($this->_itemCollection, $customLabel);
        return $this;
    }

    protected function _beforeToHtml()
    {
        $this->_prepareData();
        return parent::_beforeToHtml();
    }

    public function getItemCollection()
    {
        return $this->_itemCollection;
    }

    public function getItems()
    {
        if (is_null($this->_items) && $this->getItemCollection()) {
            $this->_items = $this->getItemCollection()->getItems();
        }
        return $this->_items;
    }

/*
    public function resetItemsIterator()
    {
        $this->getItems();
        reset($this->_items);
    }

    public function getIterableItem()
    {
        $item = current($this->_items);
        next($this->_items);
        return $item;
    }
*/

    /**
     * Get tags array for saving cache
     *
     * @return array
     */
    public function getCacheTags()
    {
        //Mage::helper('productcrosssell')->log(sprintf("%s->parent::getCacheTags(): %s", __METHOD__, print_r(parent::getCacheTags(), true)) );
        $itemTags = array();
        if ($this->getItems()) {
            $itemTags = $this->getItemsTags($this->getItems());
            //Mage::helper('productcrosssell')->log(sprintf("%s->getItemsTags(): %s", __METHOD__, print_r($itemTags, true)) );
        }
        return array_merge(parent::getCacheTags(), $itemTags);
    }

    /**
     * Get tag key for saving cache
     *
     * @return mixed
     */
    public function getCacheKey()
    {
        if (!$this->hasData('cache_key')) {
            $cacheKey = $this->_makeCacheCrosssellKey();
            $this->setCacheKey($cacheKey);
        }
        return $this->getData('cache_key');
    }

    /**
     * @return array|string
     */
    protected function _makeCacheCrosssellKey()
    {
        $cacheKey = array();

        // ID of data in the cache
        $cacheKey[] = "CROSSSELL_PRODUCT";
        // Store ID
        $cacheKey[] = Mage::app()->getStore()->getId();
        // SSL
        $cacheKey[] = (int)Mage::app()->getStore()->isCurrentlySecure();
        // Package
        $cacheKey[] = Mage::getDesign()->getPackageName();
        // Theme
        $cacheKey[] = Mage::getDesign()->getTheme('template');
        // Product ID
        $cacheKey[] = Mage::app()->getRequest()->getRequestUri();
        // Currency
        $cacheKey[] = Mage::getModel('core/cookie')->get("currency") ? Mage::getModel('core/cookie')->get("currency") : "";

        $key = array_values($cacheKey); // ignore array keys
        $key = implode('|', $key);
        //Mage::helper('productcrosssell')->log(sprintf("%s->cache_key: %s", __METHOD__, $key) );
        $key = sha1($key);

        return $key;
    }

}