<?php

/**
 * Class FactoryX_CartImage_Block_Checkout_Cart_Item_Renderer_Configurable
 */
class FactoryX_CartImage_Block_Checkout_Cart_Item_Renderer_Configurable extends Mage_Checkout_Block_Cart_Item_Renderer_Configurable
{
    /**
     * Get product thumbnail image
     *
     * @return Mage_Catalog_Model_Product_Image
     */
    public function getProductThumbnail()
    {
        if (!Mage::helper('cartimage')->isEnabled()) {
            return parent::getProductThumbnail();
        } else {
            $product = $this->getChildProduct();
            if (!$product
                || !$product->getData('thumbnail')
                || ($product->getData('thumbnail') == 'no_selection')
                || (Mage::getStoreConfig(self::CONFIGURABLE_PRODUCT_IMAGE) == self::USE_PARENT_IMAGE)
            ) {
                $product = $this->getProduct();
                $cartImage = Mage::helper('cartimage')->getMatchingThumbnail($product, $this->getOptionList());
            }
        }

        if (!$cartImage) {
            $cartImage = $this->helper('catalog/image')->init($product, 'thumbnail');
        }

        return $cartImage;
    }
}