<?php

/**
 * Class FactoryX_Framework_Model_Observer
 */
class FactoryX_Framework_Model_Observer
{
    /**
     * Add bootstrap classes to HTML on the fly
     * @param Varien_Event_Observer $observer
     */
    public function addBootstrapClasses(Varien_Event_Observer $observer)
    {
        // Get the rendered block
        $block = $observer->getBlock();

        if ($block instanceof Fontis_Australia_Block_Autocomplete) {
            $newHtml = $this->_addUnstyledLink($observer->getTransport()->getHtml());
        } else if ($block instanceof Mage_Wishlist_Block_Customer_Sidebar) {
            $newHtml = $this->_addBadge(array('<small>(',')</small>'),array('<span class="badge">','</span>'),$observer->getTransport()->getHtml());
        } else if ($block instanceof Mage_Wishlist_Block_Links) {
            $newHtml = $observer->getTransport()->getHtml();
            if (Mage::helper('framework')->isWishlistBadge()) {
                $newHtml = $this->_addBadge(array('>My Wishlist (',' item)<'),array(">My Wishlist <span class='badge'>",'</span><'),$newHtml);
                $newHtml = $this->_addBadge(array('>My Wishlist (',' items)<'),array(">My Wishlist <span class='badge'>",'</span><'),$newHtml);
            } else {
                $newHtml = str_replace(' item)',')',$newHtml);
                $newHtml = str_replace(' items)',')',$newHtml);
            }
            if ($afterText = Mage::helper('framework')->getWishlistAfter()) {
                $newHtml = str_replace('>My Wishlist','>My Wishlist ' . $afterText,$newHtml);
            }
            if ($text = Mage::helper('framework')->getWishlistText()) {
                $newHtml = str_replace('My Wishlist',$text,$newHtml);
            }
        } else if ($block instanceof MageWorx_XSitemap_Block_Page_Template_Links
            || $block instanceof Mage_Page_Block_Template_Links_Block) {
            $newHtml = $observer->getTransport()->getHtml();
            if (Mage::helper('framework')->isCartBadge()) {
                $newHtml = $this->_addBadge(array('>My Cart (',' item)<'),array(">My Cart <span class='badge'>",'</span><'),$newHtml);
                $newHtml = $this->_addBadge(array('>My Cart (',' items)<'),array(">My Cart <span class='badge'>",'</span><'),$newHtml);
            } else {
                $newHtml = str_replace(' item)',')',$newHtml);
                $newHtml = str_replace(' items)',')',$newHtml);
            }
            if ($afterText = Mage::helper('framework')->getCartAfter()) {
                $newHtml = str_replace('>My Cart','>My Cart ' . $afterText,$newHtml);
            }
            if ($text = Mage::helper('framework')->getCartText()) {
                $newHtml = str_replace('My Cart',$text,$newHtml);
            }
        } else if ($block instanceof Mage_CatalogSearch_Block_Autocomplete) {
            $newHtml = $this->_addUnstyledLink($observer->getTransport()->getHtml());
            $newHtml = str_replace('"amount"','"badge"',$newHtml);
            $newHtml = str_replace('</span>','</span> ',$newHtml);
        } else {
            $newHtml = false;
        }

        if ($newHtml) {
            // Change output HTML
            $observer->getTransport()->setHtml($newHtml);
        }
    }

    /**
     * Add the list-unstyled class to the list
     * @param $html
     * @return mixed
     */
    protected function _addUnstyledLink($html)
    {
        return str_replace("<ul>","<ul class='list-unstyled'>",$html);
    }

    /**
     * Add bootstrap badges
     * @param $html
     * @return mixed
     */
    protected function _addBadge($toReplace,$badges,$html)
    {
        return str_replace($toReplace,$badges,$html);
    }
}