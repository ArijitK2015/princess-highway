<?php
/**
 * Class FactoryX_ExtendedCatalog_Model_Observer

# cache lifetime

cache_lifetime = false

cache_lifetime = 0


 */
class FactoryX_ExtendedCatalog_Model_Observer
{
    /*
    FactoryX_ExtendedCatalog_Model_Observer::CACHE_TAG

    use alternate tags to
    Mage_Catalog_Model_Product::CACHE_TAG

    these get flushed
    Mage_Catalog_Model_Category->move()
    Mage_Catalog_Model_Product->_beforeSave()
    Mage_Catalog_Model_Product->_beforeDelete()
    Mage_Adminhtml_Catalog_Product_AttributeController->saveAction()
    */
    const CACHE_TAG = 'FX_EXTENDED_CACHE_TAG';

    /**
     *
     */
    function disableCsrf()
    {
        // Disable CSRF for all
        if (Mage::app()->getRequest()->getParam('form_key')){
            $key = Mage::getSingleton('core/session')->getFormKey();
            Mage::app()->getRequest()->setParam('form_key', $key);
        }
    }

    /**
     *
     */
    public function initProductBlockCache(Varien_Event_Observer $observer)
    {
        $block  = $observer->getEvent()->getBlock();

        if (($block instanceof Mage_Catalog_Block_Product_View || $block instanceof MageWorx_SeoSuite_Block_Catalog_Product_View || $block instanceof AW_Mobile_Block_Catalog_Product_View)
            && $block->getTemplate() == "catalog/product/view.phtml") {
            $block->addData(array(
                'cache_lifetime'    => 86400,
                'cache_tags'        => array(FactoryX_ExtendedCatalog_Model_Observer::CACHE_TAG),
                //'cache_tags'      => array(Mage_Catalog_Model_Product::CACHE_TAG),
                'cache_key'         => $this->getProductCacheKey($block)
            ));
        }
    }

    /**
     *
     */
    public function initFooterBlockCache(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();

        if ($block instanceof Mage_Page_Block_Html_Footer)
        {
            $block->addData(array(
                'cache_lifetime'    => 86400,
                'cache_tags'        => array(FactoryX_ExtendedCatalog_Model_Observer::CACHE_TAG),
                //'cache_tags'        => array(Mage_Core_Model_Store::CACHE_TAG, Mage_Cms_Model_Block::CACHE_TAG),
                'cache_key'         => $this->getFooterCacheKey($block)
            ));
        }
    }

    /**
     *
     */
    public function initCurrencyBlockCache(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Directory_Block_Currency) {
            $block->addData(array(
                'cache_lifetime'    => 86400,
                'cache_tags'        => array(FactoryX_ExtendedCatalog_Model_Observer::CACHE_TAG),
                //'cache_tags'        => array(Mage_Core_Model_Store::CACHE_TAG, Mage_Cms_Model_Block::CACHE_TAG),
                'cache_key'         => $this->getCurrencyCacheKey($block)
            ));
        }
    }

    /**
     *
     */
    public function initNavBlockCache(Varien_Event_Observer $observer)
    {
        $block  = $observer->getEvent()->getBlock();

        if ($block instanceof Mage_Catalog_Block_Navigation) {
            $query = parse_url(Mage::helper('core/url')->getCurrentUrl(), PHP_URL_QUERY);

            parse_str($query, $filters);

            if (isset($filters['q']))
            {
                $block->addData(array(
                    'cache_lifetime'    => 86400,
                    'cache_tags'        => array(FactoryX_ExtendedCatalog_Model_Observer::CACHE_TAG),
                    //'cache_tags'        => array(Mage_Catalog_Model_Category::CACHE_TAG, Mage_Core_Model_Store_Group::CACHE_TAG),
                    'cache_key'			=> $filters['q'],
                ));
            }
            else
            {
                $block->addData(array(
                    'cache_lifetime'    => 86400,
                    'cache_tags'        => array(FactoryX_ExtendedCatalog_Model_Observer::CACHE_TAG)
                    //'cache_tags'        => array(Mage_Catalog_Model_Category::CACHE_TAG, Mage_Core_Model_Store_Group::CACHE_TAG),
                ));
            }
        }
    }

    /**
     *
     */
    public function initCategoryBlockCache(Varien_Event_Observer $observer)
    {
        $block  = $observer->getEvent()->getBlock();

        // First case is for the AW_Ajaxcatalog version 1.x
        if (Mage::getConfig()->getModuleConfig('AW_Ajaxcatalog')->active
            && !Mage::getStoreConfig('advanced/modules_disable_output/AW_Ajaxcatalog')
            && Mage::getStoreConfig('awajaxcatalog/general/enabled')
            && $block instanceof AW_Ajaxcatalog_Block_Catalog_Product_List
        )
        {
            // Fix the bug where the AJAX load would stop once the cache is populated
            if (!$block->getLoadedProductCollection()->getPageSize()) {
                $toolbar = $block->getToolbarBlock();
                $toolbar->setCollection($block->getLoadedProductCollection());
            }

            $block->addData(
                array(
                    'cache_lifetime'    => 86400,
                    'cache_tags'        => $this->getCategoryCacheTag($block, array(FactoryX_ExtendedCatalog_Model_Observer::CACHE_TAG)),
                    //'cache_tags'        => array(Mage_Catalog_Model_Category::CACHE_TAG),
                    'cache_key'         => $this->getAjaxCategoryCacheKey($block)
                )
            );
        }
        // Second case is for AW_Ajaxcatalog version 2.x or default Magento
        elseif (
            ((Mage::getConfig()->getModuleConfig('AW_Ajaxcatalog')->active
                    && !Mage::getStoreConfig('advanced/modules_disable_output/AW_Ajaxcatalog')
                    && Mage::getStoreConfig('awajaxcatalog/general/enabled')
                    && version_compare((string)Mage::getConfig()->getNode()->modules->AW_Ajaxcatalog->version, "2.0.0", ">=")
                )
                ||
                (!Mage::getConfig()->getModuleConfig('AW_Ajaxcatalog')->active
                    || (string)Mage::getConfig()->getModuleConfig('AW_Ajaxcatalog')->active == 'false'
                    || Mage::getStoreConfig('advanced/modules_disable_output/AW_Ajaxcatalog')
                    || !Mage::getStoreConfig('awajaxcatalog/general/enabled')
                ))
            && $block instanceof Mage_Catalog_Block_Product_List)
        {
            $block->addData(
                array(
                    'cache_lifetime'    => 86400,
                    'cache_tags'        => $this->getCategoryCacheTag($block, array(FactoryX_ExtendedCatalog_Model_Observer::CACHE_TAG)),
                    //'cache_tags'        => array(Mage_Catalog_Model_Category::CACHE_TAG),
                    'cache_key'         => $this->getCategoryCacheKey($block)
                )
            );
        }
    }

    /**
     * makeCacheCatTag
     *
     * @return string tag
     */
    public function makeCacheCatTag() {
        $tag = sprintf("%s_%d", Mage_Catalog_Model_Category::CACHE_TAG, Mage::app()->getRequest()->getParam('id'));
        $tag = strtoupper($tag);
        //Mage::helper('extendedcatalog')->log(sprintf("%s->cacheTag=%s", __METHOD__, $tag) );
        return $tag;
    }

    public function makeCacheCatKey()
    {
        $cacheKey = array();

        // ID of data in the cache
        $cacheKey[] = sprintf("CATALOG_CATEGORY_%d", Mage::app()->getRequest()->getParam('id'));
        //$cacheKey[] = "EXTENDEDCATALOG_CATEGORY";

        // Array of parameters
        $catId = Mage::app()->getRequest()->getRequestUri();
        $arr = parse_url($catId);
        $catId = $arr['path'];

        // Cut query
        if (array_key_exists('query', $arr)) {
            parse_str($arr['query'], $params);
        }

        // Category ID
        $cacheKey[] = $catId;

        // Device
        $device = "desktop";
        if (isset($_SERVER['HTTP_USER_AGENT']) && stripos($_SERVER['HTTP_USER_AGENT'], 'ipad') !== false) {
            $device = "ipad";
        }
        $cacheKey[] = $device;

        $qs = false;
        $requestParams = array();
        if (array_key_exists('query', $arr)) {
            $requestParams = array_merge($params,$requestParams);
        }
        if(is_array($params = Mage::app()->getRequest()->getParams())) {
            $requestParams = array_merge($params,$requestParams);
        }

        // Convert params to key
        if ($convertedParams = Mage::helper('extendedcatalog')->convertParamsToKey($requestParams)) {
            $cacheKey[] = $convertedParams;
        }

        // Currency
        $cacheKey[] = Mage::app()->getStore()->getCurrentCurrency()->getCode();
        // Store ID
        $cacheKey[] = Mage::app()->getStore()->getId();
        // Package
        $cacheKey[] = Mage::getSingleton('core/design_package')->getPackageName();
        // Theme
        $cacheKey[] = Mage::getDesign()->getTheme('template');
        // JS view mode
        $cacheKey[] = Mage::getModel('core/cookie')->get('view-mode');

        $key = array_values($cacheKey); // ignore array keys
        $key = implode('|', $key);
        //Mage::helper('extendedcatalog')->log(sprintf("%s->key=%s", __METHOD__, $key) );
        $key = sha1($key);

        return $key;
    }

    /**
     * makeCacheCatTag
     *
     * @return string tag
     */
    public function makeAjaxCacheCatTag() {
        $tag = sprintf("%s_%d", Mage_Catalog_Model_Category::CACHE_TAG, Mage::app()->getRequest()->getParam('id'));
        $tag = strtoupper($tag);
        //Mage::helper('extendedcatalog')->log(sprintf("%s->cacheTag=%s", __METHOD__, $tag) );
        return $tag;
    }

    public function makeAjaxCacheCatKey()
    {
        $cacheKey = array();

        // ID of data in the cache
        $cacheKey[] = sprintf("CATALOG_CATEGORY_%d", Mage::app()->getRequest()->getParam('id'));
        //$cacheKey[] = "EXTENDEDCATALOG_CATEGORY";

        // Array of parameters
        if ($pa = Mage::app()->getRequest()->getParam('pa'))
        {
            $params = Zend_Json_Decoder::decode(base64_decode($pa));
            if ($params['route'] == "catalog")
            {
                $catId = "/catalog/category/view/id/".$params['id'];
                $category = Mage::getModel('catalog/category')->load($params['id']);
                Mage::register('current_category', $category);
            }
            elseif ($params['route'] == "catalogsearch")
            {
                $catId = "/catalogsearch/result/";
                Mage::register('current_category',
                    new Varien_Object(array(
                            'name' => 'catalogsearch',
                        )
                    ));
            }
            $arr = array();
        }
        else
        {
            // Category ID
            $catId = Mage::app()->getRequest()->getRequestUri();
            $arr = parse_url($catId);
            $catId = $arr['path'];
            $params = Mage::app()->getRequest()->getParams();
        }

        // Cut query
        if (array_key_exists('query', $arr)) {
            parse_str($arr['query'], $params);
        }

        // Category ID
        $cacheKey[] = $catId;

        // Device
        $device = "desktop";
        if (isset($_SERVER['HTTP_USER_AGENT']) && stripos($_SERVER['HTTP_USER_AGENT'], 'ipad') !== false) {
            $device = "ipad";
        }
        $cacheKey[] = $device;

        $qs = false;
        if (array_key_exists('query', $arr)) {
            // Convert params to key
            if ($convertedParams = Mage::helper('extendedcatalog')->convertParamsToKey($params)) {
                $cacheKey[] = $convertedParams;
            }

        }
        elseif(is_array($params)) {
            // Convert params to key
            if ($convertedParams = Mage::helper('extendedcatalog')->convertParamsToKey($params)) {
                $cacheKey[] = $convertedParams;
            }
        }

        // Currency
        $cacheKey[] = Mage::app()->getStore()->getCurrentCurrency()->getCode();
        // Store ID
        $cacheKey[] = Mage::app()->getStore()->getId();
        // Package
        $cacheKey[] = Mage::getSingleton('core/design_package')->getPackageName();
        // Theme
        $cacheKey[] = Mage::getDesign()->getTheme('template');
        // JS view mode
        $cacheKey[] = Mage::getModel('core/cookie')->get('view-mode');

        // Extra flag is it's an AJAX request
        if (Mage::app()->getRequest()->getParam('pa')) {
            $cacheKey[] = "pa";
        }

        $key = array_values($cacheKey); // ignore array keys
        $key = implode('|', $key);
        //Mage::helper('extendedcatalog')->log(sprintf("%s->key=%s", __METHOD__, $key) );
        $key = sha1($key);

        return $key;
    }

    public function makeCacheProdKey()
    {
        $cacheKey = array();

        // ID of data in the cache
        $cacheKey[] = "EXTENDEDCATALOG_PRODUCT";
        // Store ID
        $cacheKey[] = Mage::app()->getStore()->getId();
        // SSL
        $cacheKey[] = (int)Mage::app()->getStore()->isCurrentlySecure();
        // Package
        $cacheKey[] = Mage::getDesign()->getPackageName();
        // Theme
        $cacheKey[] = Mage::getDesign()->getTheme('template');
        // Logged In ?
        $cacheKey[] = Mage::getSingleton('customer/session')->isLoggedIn();
        // Product ID
        $cacheKey[] = Mage::app()->getRequest()->getRequestUri();
        // Currency
        $cacheKey[] = Mage::app()->getStore()->getCurrentCurrency()->getCode();;

        $key = array_values($cacheKey); // ignore array keys
        $key = implode('|', $key);
        $key = sha1($key);

        return $key;
    }

    public function makeCacheFootKey()
    {
        $cacheKey = array();

        // ID of data of the cache
        $cacheKey[] = "PAGE_FOOTER";
        // Store ID
        $cacheKey[] = Mage::app()->getStore()->getId();
        // SSL
        $cacheKey[] = (int)Mage::app()->getStore()->isCurrentlySecure();
        // Package
        $cacheKey[] = Mage::getDesign()->getPackageName();
        // Theme
        $cacheKey[] = Mage::getDesign()->getTheme('template');
        // Logged In ?
        $login = Mage::getSingleton('customer/session')->isLoggedIn();
        $cacheKey[] = $login;
        if($login){
            $wishlist = Mage::helper('wishlist')->getItemCount();
            if($wishlist) $cacheKey[] = $wishlist;
        }

        // currency AND url (the currency dropdown is used to switch the url)
        $cacheKey[] = $currency = Mage::app()->getStore()->getCurrentCurrency()->getCode();
        $cacheKey[] = Mage::helper('directory/url')->getSwitchCurrencyUrl(array('currency' => $currency));

        $key = array_values($cacheKey); // ignore array keys
        $key = implode('|', $key);
        $key = sha1($key);

        return $key;
    }

    public function makeCacheCurrencyKey($blockNameInLayout)
    {
        $cacheKey = [];

        // Block Name in layout
        $cacheKey[] = $blockNameInLayout;
        // ID of data of the cache
        $cacheKey[] = "PAGE_CURRENCY";
        // Store ID
        $cacheKey[] = Mage::app()->getStore()->getId();
        // SSL
        $cacheKey[] = (int)Mage::app()->getStore()->isCurrentlySecure();
        // Package
        $cacheKey[] = Mage::getDesign()->getPackageName();
        // Theme
        $cacheKey[] = Mage::getDesign()->getTheme('template');
        // Logged In ?
        $cacheKey[] = Mage::getSingleton('customer/session')->isLoggedIn();

        // currency AND url (the currency dropdown is used to switch the url)
        $cacheKey[] = $currency = Mage::app()->getStore()->getCurrentCurrency()->getCode();
        $cacheKey[] = Mage::helper('directory/url')->getSwitchCurrencyUrl(['currency' => $currency]);

        $key = array_values($cacheKey); // ignore array keys
        $key = implode('|', $key);
        $key = sha1($key);

        return $key;
    }

    /**
     * getCategoryCacheTag
     *
     * @param object $block
     * @return array $addtionalTags
     */
    public function getCategoryCacheTag($block, $addtionalTags)
    {
        if (!$block->hasData('cache_tag'))
        {
            array_push($addtionalTags, $this->makeCacheCatTag());
            //$addtionalTags = array($this->makeCacheCatTag());
            //Mage::helper('extendedcatalog')->log(sprintf("%s->addtionalTags=%s", __METHOD__, print_r($addtionalTags, true)) );
            $block->setCacheTag($addtionalTags);
        }
        return $block->getData('cache_tag');
    }

    public function getCategoryCacheKey($block)
    {
        if (!$block->hasData('cache_key'))
        {
            $cacheKey = $this->makeCacheCatKey();
            $block->setCacheKey($cacheKey);
        }
        return $block->getData('cache_key');
    }

    public function getAjaxCategoryCacheKey($block)
    {
        if (!$block->hasData('cache_key'))
        {
            $cacheKey = $this->makeAjaxCacheCatKey();
            $block->setCacheKey($cacheKey);
        }
        return $block->getData('cache_key');
    }

    public function getFooterCacheKey($block)
    {
        if (!$block->hasData('cache_key'))
        {
            $cacheKey = $this->makeCacheFootKey();
            $block->setCacheKey($cacheKey);
        }
        return $block->getData('cache_key');
    }

    public function getCurrencyCacheKey($block)
    {
        if (!$block->hasData('cache_key'))
        {
            $cacheKey = $this->makeCacheCurrencyKey($block->getNameInLayout());
            $block->setCacheKey($cacheKey);
        }
        return $block->getData('cache_key');
    }

    public function getProductCacheKey($block)
    {
        if (!$block->hasData('cache_key')) {
            $cacheKey = $this->makeCacheProdKey();
            $block->setCacheKey($cacheKey);
        }
        return $block->getData('cache_key');
    }
}