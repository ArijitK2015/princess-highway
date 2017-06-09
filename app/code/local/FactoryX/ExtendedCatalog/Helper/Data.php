<?php

class FactoryX_ExtendedCatalog_Helper_Data extends Mage_Core_Helper_Abstract
{

    const XML_CONFIG_GRID_X5_IMAGE_DIMENSIONS = "catalog/frontend/gridx5_image_dimensions";
    const XML_CONFIG_REPLACE_DEFAULT_OPTION_TEXT = "catalog/frontend/replace_default_option_text";
    const XML_CONFIG_POSITION_TEXT = "catalog/frontend/position_text";

    protected $logFileName = 'factoryx_extendedcatalog.log';

    public function getImageDimensions()
    {
        $imageDimensions = array();
        $d = Mage::getStoreConfig(self::XML_CONFIG_GRID_X5_IMAGE_DIMENSIONS);
        if (preg_match("/\d{1,},\d{1,}/", $d)) {
            $imageDimensions = explode(",", $d, 2);
        }
        return $imageDimensions;
    }

    public function replaceDefaultOptionText()
    {
        return Mage::getStoreConfigFlag(self::XML_CONFIG_REPLACE_DEFAULT_OPTION_TEXT);
    }

    public function getPositionText()
    {
        return Mage::getStoreConfig(self::XML_CONFIG_POSITION_TEXT);
    }

    public function isAwOverride()
    {
        return ($this->replaceDefaultOptionText() && (string)Mage::getConfig()->getModuleConfig('AW_Ajaxcartpro')->active) ? "aw_ajaxcartpro/product.js" : null;
    }

    /**
     * @param $params
     * @return bool|string
     */
    public function convertParamsToKey($params)
    {
        $query = array();
        $allowedKeys = array(
            // toolbar
            'dir','order','limit','q','p','mode','cat'
        );
        // Add filterable attributes' codes
        $collection = Mage::getResourceModel('catalog/product_attribute_collection')
            ->addStoreLabel(Mage::app()->getStore()->getId())
            ->addIsFilterableFilter();

        foreach($collection as $item) {
            $allowedKeys[] = $item->getAttributeCode();
        }
        /*
         * Suport for AW layered navigation attributes
         * Some attributes might not be filterable (so not added via the code above)
         * But still available from the AW layered navigation
         */
        if ((string)Mage::getConfig()->getModuleConfig('AW_Layerednavigation')->active == 'true'
            && Mage::helper('aw_layerednavigation/config')->isEnabled()) {
            $storeId = Mage::app()->getStore()->getId();
            $collection = Mage::getResourceModel('aw_layerednavigation/filter_collection')
                ->addFilterAttributes($storeId)
                ->addFieldToSelect('code')
                ->addFieldToFilter(
                    array('is_enabled_ds.value','is_enabled_in_search_ds.value'),
                    array(
                        array('eq'  =>  1),
                        array('eq'  =>  1)
                    )
                );
            foreach ($collection as $item) {
                if (!in_array($item->getCode(), $allowedKeys)) {
                    $allowedKeys[] = $item->getCode();
                }
            }
        }
        
        foreach($allowedKeys as $key) {
            if (array_key_exists($key, $params)) {
                $query[$key] = $params[$key];
            } else {
                // Handle session values
                switch ($key) {
                    case 'mode':
                        $query[$key] = $this->_getDefaultMode();
                        break;
                    case 'dir':
                        $query[$key] = Mage::getSingleton('catalog/session')->getSortDirection() ? Mage::getSingleton('catalog/session')->getSortDirection() : 'asc';
                        break;
                    case 'order':
                        $query[$key] = Mage::getSingleton('catalog/session')->getSortOrder() ? Mage::getSingleton('catalog/session')->getSortOrder() : $this->_getDefaultOrder();
                        break;
                    case 'limit':
                        $query[$key] = Mage::getSingleton('catalog/session')->getLimitPage() ? Mage::getSingleton('catalog/session')->getLimitPage() : $this->_getDefaultLimit();
                        break;
                    default:
                        break;
                }
            }
        }
        if (count($query)) {
            return http_build_query($query);
        } else {
            return false;
        }
    }

    public function getBundleOptions()
    {
        $product = Mage::registry('current_product');
        $optionCollection = $product->getTypeInstance()->getOptionsCollection();
        $selectionCollection = $product->getTypeInstance()->getSelectionsCollection($product->getTypeInstance()->getOptionsIds());
        $options = $optionCollection->appendSelections($selectionCollection);
        foreach( $options as $option )
        {
            $_selections = $option->getSelections();
            $simpleId = $option->getId();

            foreach( $_selections as $selection )
            {
                $product_simple = Mage::getModel('catalog/product')->load($selection->getId());
                $colour = $product_simple->getAttributeText('colour_description');
                $sizes = array(
                    'size_06_11',
                    'size_06_16' ,
                    'size_36_42',
                    'size_sm_ml',
                    'size_os'
                );

                foreach($product_simple->getAttributes() as $a)
                {
                    if(in_array($a->getAttributeCode(),$sizes))
                    {
                        $size = $product_simple->getAttributeText($a->getAttributeCode());
                        break;
                    }
                }

                $products['bundle_product'][$simpleId][$colour][$size]['colour'] = $colour;
                $products['bundle_product'][$simpleId][$colour][$size]['size'] = $size;
                $products['bundle_product'][$simpleId][$colour][$size]['price'] = $product_simple->getFinalPrice();
                $products['bundle_product'][$simpleId][$colour][$size]['default'] = 0;
                $products['bundle_product'][$simpleId][$colour][$size]['selectionid'] = $selection->getSelectionId();


                $parentIds = Mage::getResourceSingleton('catalog/product_type_configurable')->getParentIdsByChild($product_simple->getId());
                $products['bundle_img'][$simpleId][$colour]['img'] = "";

                foreach($parentIds as $parent)
                {
                    $parentProduct = Mage::getModel('catalog/product')->load($parent);
                    foreach($parentProduct->getMediaGalleryImages() as $img)
                    {
                        $isFront = strpos($img->getLabel(), "front_".strtolower(str_replace(" ","-",trim($colour))));
                        if($isFront === false)
                        {
                            $isFront = strpos($img->getFile(), "f.");

                        }
                        if($isFront !== false)
                        {
                            $a = Mage::helper('catalog/image')->init($parentProduct, 'thumbnail',$img->getFile())->resize(75,75);
                            $products['bundle_img'][$simpleId][$colour]['img']=str_replace(Mage::getBaseUrl(),"",$a."");
                            break;
                        }
                    }
                }
            }
        }

        return $products;
    }

    /**
     * Retrieve url for direct adding product to cart
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($product)
    {
        $url = Mage::helper('checkout/cart')->getAddUrl($product);
        $url = str_replace('/checkout/', '/awmobile/', $url);
        return $url;
    }

    /**
     * Retrives product price
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getPrice($product)
    {
        return $product->getPriceModel()->getFormatedPrice($product);
    }

    /**
     * Log data
     * @param string|object|array data to log
     */
    public function log($data) {
        Mage::log($data, null, $this->logFileName);
    }

    protected function _getDefaultMode()
    {
        if (Mage::getSingleton('catalog/session')->getDisplayMode())
        {
            return Mage::getSingleton('catalog/session')->getDisplayMode();
        }
        else
        {
            $displayModes = Mage::getStoreConfig('catalog/frontend/list_mode');
            if (preg_match("/-/", $displayModes)) {
                $modes = preg_split("/-/", $displayModes);
                return $modes[0];
            }
            else
            {
                return $displayModes;
            }
        }
    }

    protected function _getDefaultOrder()
    {
        $availableOrders = Mage::getBlockSingleton('catalog/product_list_toolbar')->getAvailableOrders();
        $keys = array_keys($availableOrders);
        return $keys[0];
    }

    protected function _getDefaultLimit()
    {
        return Mage::getStoreConfig('catalog/frontend/'.$this->_getDefaultMode().'_per_page');
    }

    function getFilteredColor($colourAttribute = 'colour_all') {
        $uri = $_SERVER['REQUEST_URI'];
        $arr = parse_url($uri);
        // OG magento
        if (array_key_exists('query', $arr)) {
            // Cut query
            parse_str($arr['query'], $arr);
            if (array_key_exists($colourAttribute, $arr)) {
                return $arr[$colourAttribute];
            }
        }
        // MageWorx SeoSuite rewrite (see _setHiddenAttributeToRequest function under app/code/local/MageWorx/SeoSuite/Controller/Router.php)
        if ($color = Mage::getModel('core/session')->getFilteredColor()) {
            return $color;
        }
        return null;
    }

    /*
     * Detect iPhone and iPad
     */
    function isiPad($user_agent = NULL) {
        if(!isset($user_agent)) {
            $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        }
        return stripos($user_agent, 'ipad') !== FALSE;
    }


}
