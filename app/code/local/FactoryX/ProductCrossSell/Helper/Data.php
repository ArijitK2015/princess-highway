<?php

/**
 * Class FactoryX_ProductCrossSell_Helper_Data
 */
class FactoryX_ProductCrossSell_Helper_Data extends Mage_Core_Helper_Abstract
{
    const DEFAULT_IMAGE_LABEL = 'front';
    const DEFAULT_IMAGE_LABEL_DIV = '_';

    const SKU_CATEGORY_POS = 0;
    const SKU_CATEGORY_LEN = 3;

    // add human recognisable category labels
    protected $_categoryLabels = array(
        // 'gwf' =>  'Accessories'
    );

    /**
     * @var string
     */
    protected $logFileName = 'factoryx_productcrosssell.log';

    /**
     * Log data
     * @param string|object|array data to log
     */
    public function log($data)
    {
        Mage::log($data, null, $this->logFileName);
    }

    /**
     * @return mixed
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag('productcrosssell/general/enable');
    }

    /**
     * @return mixed
     */
    public function isEnabledUpsells()
    {
        return Mage::getStoreConfigFlag('productcrosssell/upsells/enable');
    }

    /**
     * @return mixed
     */
    public function isEnabledRecentlyViewed()
    {
        return Mage::getStoreConfigFlag('productcrosssell/recently/enable');
    }

    /**
     * @return mixed
     */
    public function isEnabledRelatedProducts()
    {
        return Mage::getStoreConfigFlag('productcrosssell/related/enable');
    }

    /**
     * @return mixed
     */
    public function getCustomTitleUpsells()
    {
        return Mage::getStoreConfig('productcrosssell/upsells/custom_title');
    }

    /**
     * @return mixed
     */
    public function getCustomTitleCrossSells()
    {
        return Mage::getStoreConfig('productcrosssell/general/custom_title');
    }

    /**
     * @return string
     */
    public function getCustomTitleRecentlyViewed()
    {
        $title = "recently viewed";
        if (Mage::getStoreConfig('productcrosssell/recently/custom_title')) {
            $title = Mage::getStoreConfig('productcrosssell/recently/custom_title');
        }
        return $title;
    }

    /**
     * @return string
     */
    public function getCustomTitleRelated()
    {
        $title = "related products";
        if (Mage::getStoreConfig('productcrosssell/related/custom_title')) {
            $title = Mage::getStoreConfig('productcrosssell/related/custom_title');
        }
        return $title;
    }

    /**
     * getCustomImageUpsells
     */
    public function getCustomImageUpsells() {
        $customImage = false;
        if (Mage::getStoreConfig('productcrosssell/upsells/custom_image_label')) {
            $customImage = Mage::getStoreConfig('productcrosssell/upsells/custom_image_label');
        }
        return $customImage;
    }

    /**
     * getCustomImageCrossSells
     */
    public function getCustomImageCrossSells() {
        $customImage = false;
        if (Mage::getStoreConfig('productcrosssell/general/custom_image_label')) {
            $customImage = Mage::getStoreConfig('productcrosssell/general/custom_image_label');
        }
        return $customImage;
    }

    /**
     * getCustomImageRecentlyViewed
     */
    public function getCustomImageRecentlyViewed() {
        $customImage = false;
        if (Mage::getStoreConfig('productcrosssell/recently/custom_image_label')) {
            $customImage = Mage::getStoreConfig('productcrosssell/recently/custom_image_label');
        }
        return $customImage;
    }

    /**
     * getCustomImageRelatedProducts
     */
    public function getCustomImageRelatedProducts() {
        $customImage = false;
        if (Mage::getStoreConfig('productcrosssell/related/custom_image_label')) {
            $customImage = Mage::getStoreConfig('productcrosssell/related/custom_image_label');
        }
        return $customImage;
    }

    /**
     * addCustomImage
     *
     * iterates each product in the collection
     * and sets the attribute 'hero_image_src' with a replacement of the 'front' thumbnail
     * e.g. hero_image_src = $customLabel_blue where thumbnail = front_blue
     *
     * @params $collection Mage_Catalog_Model_Resource_Product_Link_Product_Collection
     * @params $customLabel string
     */
    public function addCustomImage($collection, $customLabel) {
        //$this->log(sprintf("%s->customLabel: %s", __METHOD__, $customLabel) );

        // this does not work, as catalog_product_flat_1 is queried, set used_in_product_listing = 1
        //$collection->addAttributeToSelect('sku');
        //$this->log(sprintf("%s->SQL: %s", __METHOD__, $collection->getSelect()->__toString()) );

        $orgCustomLabel = $customLabel;
        foreach ($collection as $product) {
            //$this->log(sprintf("%s->product: %s [%s]", __METHOD__, $product->getId(), $product->getSku()) );

            // set if sku is mapped
            if ($customLabelNew = $this->getProductCategoryImage($product->getSku())) {
                $customLabel = $customLabelNew;
                //$this->log(sprintf("%s->customLabelNew: %s [%s]", __METHOD__, $customLabel, $product->getSku()) );
            }
            else {
                $customLabel = $orgCustomLabel;
            }
            $label = $this->getImageLabel($product, 'thumbnail');
            //$this->log(sprintf("%s->label: %s", __METHOD__, $label) );

            // get the media gallery (without loading the entire product)
            $product->getResource()->getAttribute('media_gallery')->getBackend()->afterLoad($product);
            $mediaGallery = $product->getMediaGalleryImages();
            //$this->log(sprintf("%s->mediaGallery: %s", __METHOD__, print_r($mediaGallery, true)) );

            // Get the file by label
            $image = $mediaGallery->getItemByColumnValue('label', $label);
            if ($image instanceof Varien_Object) {
                $file = $image->getFile();
                if (preg_match(sprintf("/^%s%s/", self::DEFAULT_IMAGE_LABEL, self::DEFAULT_IMAGE_LABEL_DIV), $label)) {
                    $newLabel = preg_replace(sprintf("/^%s/", self::DEFAULT_IMAGE_LABEL), $customLabel, $label);
                    //$this->log(sprintf("%s->newLabel: %s", __METHOD__, $newLabel) );
                    if ($newProductImage = $mediaGallery->getItemByColumnValue('label', $newLabel)) {
                        $file = $newProductImage->getFile();
                        //$this->log(sprintf("%s->file: %s", __METHOD__, $file) );
                        //$file = $newProductImage->getUrl();
                        $product->setData("hero_image_src", $file);
                    }
                }
            }
        }
    }

    /**
     * Retrieve given media attribute label or product name if no label
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $mediaAttributeCode
     *
     * @return string
     */
    public function getImageLabel($product = null, $mediaAttributeCode = 'image')
    {
        $label = $product->getData($mediaAttributeCode . '_label');
        if (empty($label)) {
            $label = $product->getName();
        }
        return $label;
    }


    /**
     * @param $fileName
     * @param $width
     * @param string $height
     * @return string
     */
    public function resizeImg($fileName, $width, $height = '') {
        $folderURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
        $imageURL = $folderURL . $fileName;
        $basePath = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . $fileName;
        $newPath = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . "resized" . DS . $fileName;
        //if width empty then return original size image's URL
        if ($width != '') {
            //if image has already resized then just return URL
            if (file_exists($basePath) && is_file($basePath) && !file_exists($newPath)) {
                $imageObj = new Varien_Image($basePath);
                $imageObj->constrainOnly(TRUE);
                $imageObj->keepAspectRatio(FALSE);
                $imageObj->keepFrame(FALSE);
                $imageObj->resize($width, $height);
                $imageObj->save($newPath);
            }
            $resizedURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . "resized" . DS . $fileName;
        }
        else {
            $resizedURL = $imageURL;
        }
        return $resizedURL;
    }

    /**
     * @return array
     */
    public function getProductCategories()
    {
        $cats = array();
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $query = "select lower(mid(sku, 1, 3)) as cat from {$resource->getTableName('catalog_product_entity')} where sku like 'g%' AND type_id in ('simple','configurable') group by cat;";
        $results = $readConnection->query($query);
        while ($row = $results->fetch()) {
            $cats[$row['cat']] = array_key_exists($row['cat'], $this->_categoryLabels) ? $this->_categoryLabels[$row['cat']] : $row['cat'];
        }
        return $cats;
    }

    /**
     * @return mixed
     */
    protected function _getMappedCategoryImages()
    {
        return unserialize(Mage::getStoreConfig('productcrosssell/category/mapping',Mage::app()->getStore()->getStoreId()));
    }

    /**
     * @param $sku
     * @return bool
     */
    public function getProductCategoryImage($sku)
    {
        $cat = strtolower(substr($sku, self::SKU_CATEGORY_POS, self::SKU_CATEGORY_LEN));
        //$this->log(sprintf("%s->cat: %s [%s]", __METHOD__, $cat, $sku));
        $mapping = $this->_getMappedCategoryImages();
        
        //It will throw an error when no array returns. 
        if(!$mapping) return false;

        foreach ($mapping as $map)
        {
            //$this->log(sprintf("%s->%s <> %s = %d", __METHOD__, $map['categories'], $cat, strcmp($map['categories'], $cat)));
            if (strcmp($map['categories'], $cat) == 0) {
                //$this->log(sprintf("%s->images: %s", __METHOD__, $map['images']));
                return $map['images'];
            }
            else {
                return false;
            }
        }
    }

}