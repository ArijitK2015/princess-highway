<?php
if (Mage::helper('core')->isModuleEnabled("FactoryX_ImageCdn") && class_exists('FactoryX_ImageCdn_Model_Catalog_Product_Image')) {
    class ImageClass extends FactoryX_ImageCdn_Model_Catalog_Product_Image { }
} else {
    class ImageClass extends Mage_Catalog_Model_Product_Image { }
}
?>

<?php
/**
 * Based on /app/code/core/Mage/Catalog/Model/Product/Image.php
 *
 * Note: for translations, the catalog helper is used.
 */
class FactoryX_Lookbook_Model_Lookbook_Image extends ImageClass
{
    /**
     * Set filenames for base file and new file
     *
     * @param string $file
     * @throws Exception
     * @return FactoryX_Lookbook_Model_Lookbook_Image
     */
    public function setBaseFile($file)
    {
        $this->_isBaseFilePlaceholder = false;

        if (($file) && (0 !== strpos($file, '/', 0))) {
            $file = '/' . $file;
        }
        $baseDir = Mage::getSingleton('lookbook/lookbook_media_config')->getBaseMediaPath();

        if ('/no_selection' == $file) {
            $file = null;
        }
        if (!empty($file)) {
            if ((!$this->_fileExists($baseDir . $file)) || !$this->_checkMemory($baseDir . $file)) {
                $baseUrl = Mage::getSingleton('lookbook/lookbook_media_config')->getBaseMediaUrl();
                if (file_exists($baseUrl . $file)) {
                    $baseDir = $baseUrl;
                }else {
                    $file = null;
                };
            }
        }
        if (empty($file)) {
            /**
             * To avoid having placeholders all over the place, use the one that
             * is default in Magento; the one for the catalog.
             */
            $catalogBaseDir = Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath();
            // check if placeholder defined in config
            $isConfigPlaceholder = Mage::getStoreConfig("catalog/placeholder/{$this->getDestinationSubdir()}_placeholder");
            $configPlaceholder   = '/placeholder/' . $isConfigPlaceholder;
            if ($isConfigPlaceholder && $this->_fileExists($catalogBaseDir . $configPlaceholder)) {
                $file = $configPlaceholder;
            }
            else {
                // replace file with skin or default skin placeholder
                $skinBaseDir     = Mage::getDesign()->getSkinBaseDir();
                $skinPlaceholder = "/images/catalog/product/placeholder/{$this->getDestinationSubdir()}.jpg";
                $file = $skinPlaceholder;
                if (file_exists($skinBaseDir . $file)) {
                    $catalogBaseDir = $skinBaseDir;
                }
                else {
                    $catalogBaseDir = Mage::getDesign()->getSkinBaseDir(array('_theme' => 'default'));
                    if (!file_exists($catalogBaseDir . $file)) {
                        $catalogBaseDir = Mage::getDesign()->getSkinBaseDir(array('_theme' => 'default', '_package' => 'base'));
                    }
                }
            }
            $this->_isBaseFilePlaceholder = true;
            $baseFile = $catalogBaseDir . $file;
        } else {
            $baseFile = $baseDir . $file;
        }

        if (empty($file) || (!file_exists($baseFile))) {
            throw new Exception(Mage::helper('catalog')->__('Image file was not found.'));
        }

        $this->_baseFile = $baseFile;

        // build new filename (most important params)
        $path = array(
            $baseDir,
            'cache',
            Mage::app()->getStore()->getId(),
            $path[] = $this->getDestinationSubdir()
        );
        if((!empty($this->_width)) || (!empty($this->_height)))
            $path[] = "{$this->_width}x{$this->_height}";

        // add misk params as a hash
        $miscParams = array(
            ($this->_keepAspectRatio  ? '' : 'non') . 'proportional',
            ($this->_keepFrame        ? '' : 'no')  . 'frame',
            ($this->_keepTransparency ? '' : 'no')  . 'transparency',
            ($this->_constrainOnly ? 'do' : 'not')  . 'constrainonly',
            $this->_rgbToString($this->_backgroundColor),
            'angle' . $this->_angle,
            'quality' . $this->_quality
        );

        // if has watermark add watermark params to hash
        if ($this->getWatermarkFile()) {
            $miscParams[] = $this->getWatermarkFile();
            $miscParams[] = $this->getWatermarkImageOpacity();
            $miscParams[] = $this->getWatermarkPosition();
            $miscParams[] = $this->getWatermarkWidth();
            $miscParams[] = $this->getWatermarkHeigth();
        }

        $path[] = md5(implode('_', $miscParams));

        // append prepared filename
        $this->_newFile = implode('/', $path) . $file; // the $file contains heading slash


        return $this;
    }

    public function clearCache()
    {
        $directory = Mage::getBaseDir('media') . DS.'lookbook'.DS.'cache'.DS;
        $io = new Varien_Io_File();
        $io->rmdir($directory, true);

        Mage::helper('core/file_storage_database')->deleteFolder($directory);
    }

}
