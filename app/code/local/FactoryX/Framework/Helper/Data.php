<?php

/**
 * Class FactoryX_Framework_Helper_Data
 */
class FactoryX_Framework_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_modeFaMatcher = array('grid'    =>  'th-large','list' =>  'list','gridx5'  => 'th');

    /**
     * @param $mode
     * @return mixed
     */
    public function getFaViewSwitcher($mode)
    {
        return $this->_modeFaMatcher[$mode];
    }

    /**
     * @return mixed
     */
    public function isFixedNavbar()
    {
        return Mage::getStoreConfigFlag('framework/options/fixed_navbar');
    }

    /**
     * @return mixed
     */
    public function isFaEnabled()
    {
        return Mage::getStoreConfigFlag('framework/options/enable_fontawesome');
    }

    /**
     * @return mixed
     */
    public function isBootstrapJsEnabled()
    {
        return Mage::getStoreConfigFlag('framework/options/enable_bootstrap_js');
    }

    /**
     * @return string
     */
    public function getGoogleFontLink()
    {
        return strtr(Mage::getStoreConfig('framework/options/google_font_link'),array('http:'   =>  '','https:'  =>  ''));
    }

    /**
     * @return bool
     */
    public function isWishlistBadge()
    {
        return ("badge" == Mage::getStoreConfig('framework/toplinks/wishlist')) ? true : false;
    }

    /**
     * @return mixed
     */
    public function getWishlistAfter()
    {
        return Mage::getStoreConfig('framework/toplinks/wishlist_after');
    }

    /**
     * @return mixed
     */
    public function getWishlistText()
    {
        return Mage::getStoreConfig('framework/toplinks/wishlist_text');
    }

    /**
     * @return bool
     */
    public function isCartBadge()
    {
        return ("badge" == Mage::getStoreConfig('framework/toplinks/cart')) ? true : false;
    }

    /**
     * @return mixed
     */
    public function getCartAfter()
    {
        return Mage::getStoreConfig('framework/toplinks/cart_after');
    }

    /**
     * @return mixed
     */
    public function getCartText()
    {
        return Mage::getStoreConfig('framework/toplinks/cart_text');
    }

    /**
     * @return string
     */
    public function getGoogleFontName()
    {
        $css = "";
        $googleFonts = explode(',',Mage::getStoreConfig('framework/options/google_font_name'));
        // only add style tag if a font has been set
        if ($googleFonts && is_array($googleFonts) && count($googleFonts) && !empty($googleFonts[0])) {
            $css = '<style type="text/css">body {font-family: ';
            foreach ($googleFonts as $googleFont) {
                $css .= '"' . $googleFont . '",';
            }
            $css .= 'serif;}</style>';
        }
        return $css;
    }

    /**
     * Get the touch icon based on size
     * @param $size
     * @return string
     */
    public function getTouchIcon($size)
    {
        if ($icon = Mage::getStoreConfig('framework/icons/icon_'.$size)) {
            return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'touch-icons/' . $icon;
        } else {
            return false;
        }
    }
}