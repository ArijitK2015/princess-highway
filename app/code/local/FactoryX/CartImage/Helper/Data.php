<?php

/**
 * Class FactoryX_CartImage_Helper_Data
 */
class FactoryX_CartImage_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $logFileName = 'factoryx_cartimage.log';

    public function getSuperAttibutesArray()
    {
        $superAttibutes = Mage::getModel('cartimage/system_config_source_superAttributes')->toOptionArray();
        return $superAttibutes;
    }

    public function isEnabled()
    {
        return Mage::getStoreConfigFlag('cartimage/general/enable');
    }

    public function getCustomAttribute()
    {
        return Mage::getStoreConfig('cartimage/general/custom_attribute');
    }

    public function getCustomImageLabel()
    {
        return Mage::getStoreConfig('cartimage/general/custom_image_label');
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param array $options
     * @return string
     */
    public function getMatchingThumbnail(Mage_Catalog_Model_Product $product, $options)
    {
        $cartImage = "";

        $attributeValue = $this->findSelectedColour($options);

        // Only if the product attribute is set
        if ($attributeValue) {
            // Backend model to avoid loading the entire product
            /** @var Mage_Eav_Model_Entity_Attribute_Backend_Abstract $backendModel */
            $backendModel = $product->getResource()->getAttribute('media_gallery')->getBackend();
            $backendModel->afterLoad($product);

            // Get the media gallery images
            /** @var Varien_Data_Collection $mediaGallery */
            $mediaGallery = $product->getMediaGalleryImages();

            $imageLabel = $this->generateImageLabel($attributeValue);

            // Find the corresponding main image
            /** @var Varien_Object $image */
            $image = $mediaGallery->getItemByColumnValue('label', $imageLabel);

            // Check if an actual image was returned by getItemByColumnValue
            if ($image instanceof Varien_Object) {
                // return the right product thumbnail
                $cartImage = Mage::helper('catalog/image')->init($product, 'thumbnail', $image->getFile());
            }
        }

        return $cartImage;
    }


    /**
     * Log data
     * @param string|object|array data to log
     */
    public function log($data)
    {
        /*
        $backtrace = debug_backtrace();
        if ($backtrace[1]['function']) {
            $data = sprintf("%s->%s", $backtrace[1]['function'], $data);
        }
        */
        Mage::log($data, null, $this->logFileName);
    }

    /**
     * @param $options
     * @return string
     */
    private function findSelectedColour($options)
    {
        $attributeValue = false;
        foreach ($options as $option) {
            if (preg_match(sprintf("/%s/i", $option['label']), $this->getCustomAttribute())) {
                $attributeValue = strtolower($option['value']);
                break;
            }
        }

        return $attributeValue;
    }

    /**
     * @param $attributeValue
     * @return string
     */
    protected function generateImageLabel($attributeValue)
    {
        return sprintf(
            "%s_%s",
            $this->getCustomImageLabel(),
            strtolower($attributeValue)
        );
    }

}
