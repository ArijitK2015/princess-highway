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
        $cartImage = "";
        $product = $this->getChildProduct();
        if (!$product
            || !$product->getData('thumbnail')
            || ($product->getData('thumbnail') == 'no_selection')
            || (Mage::getStoreConfig(self::CONFIGURABLE_PRODUCT_IMAGE) == self::USE_PARENT_IMAGE)
           ) {
            $product = $this->getProduct();

            // Get product options
            $options = $this->getOptionList();
            // Find the colour selected
            foreach ($options as $option) {
                //Mage::helper('cartimage')->log(sprintf("%s-> %s <> %s", __METHOD__, Mage::helper('cartimage')->getCustomAttribute(), $option['label']));
                if (preg_match(sprintf("/%s/i", $option['label']), Mage::helper('cartimage')->getCustomAttribute())) {
                    $attributeValue = strtolower($option['value']);
                    break;
                }
            }

            // Only if the product attribute is set
            if (isset($attributeValue)) {
                //Mage::helper('cartimage')->log(sprintf("%s->attributeValue: %s", __METHOD__, $attributeValue));
                
                // Backend model to avoid loading the entire product
                /** @var Mage_Eav_Model_Entity_Attribute_Backend_Abstract $backendModel */
                $backendModel = $product->getResource()->getAttribute('media_gallery')->getBackend();
                $backendModel->afterLoad($product);

                // Get the media gallery images
                /** @var Varien_Data_Collection $mediaGallery */
                $mediaGallery = $product->getMediaGalleryImages();

                // Find the corresponding main image
                /** @var Varien_Object $image */
                
                $imageLabel = sprintf("%s_%s", Mage::helper('cartimage')->getCustomImageLabel(), strtolower($attributeValue));
                //Mage::helper('cartimage')->log(sprintf("%s->imageLabel: %s", __METHOD__, $imageLabel));
                
                // FactoryX_CartImage_Block_Checkout_Cart_Item_Renderer_Configurable
                $image = $mediaGallery->getItemByColumnValue('label', $imageLabel);
                //Mage::helper('cartimage')->log(sprintf("%s->image: %s", __METHOD__, get_class($image)));
                
                // check if an actual image was returned by getItemByColumnValue
                if ($image instanceof Varien_Object) {
                    // return the right product thumbnail
                    $cartImage = $this->helper('catalog/image')->init($product, 'thumbnail', $image->getFile());
                }
            }
        }
        // use default
        if (!$cartImage) {
            $cartImage = $this->helper('catalog/image')->init($product, 'thumbnail');
        }
        return $cartImage;
    }
}