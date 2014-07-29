<?php
/**
 * Rename image functionality
 */
class FactoryX_ExtendedApi_Model_Catalog_Product_Attribute_Media_Api extends Mage_Catalog_Model_Product_Attribute_Media_Api
{
    /**
     * Update image data
     *
     * @param int|string $productId
     * @param string $file
     * @param array $data
     * @param string|int $store
     * @return boolean
     */
    public function update($productId, $file, $data, $store = null, $identifierType = null)
    {
        $data = $this->_prepareImageData($data);

        $product = $this->_initProduct($productId, $store, $identifierType);

        $gallery = $this->_getGalleryAttribute($product);

        if (!$gallery->getBackend()->getImage($product, $file)) {
            $this->_fault('not_exists');
        }

        if (isset($data['file']['mime']) && isset($data['file']['content'])) {
            if (!isset($this->_mimeTypes[$data['file']['mime']])) {
                $this->_fault('data_invalid', Mage::helper('catalog')->__('Invalid image type.'));
            }

            $fileContent = @base64_decode($data['file']['content'], true);
            if (!$fileContent) {
                $this->_fault('data_invalid', Mage::helper('catalog')->__('Image content is not valid base64 data.'));
            }

            unset($data['file']['content']);

            $ioAdapter = new Varien_Io_File();
            try {
                $fileName = Mage::getBaseDir('media'). DS . 'catalog' . DS . 'product' . $file;
                $ioAdapter->open(array('path'=>dirname($fileName)));
                $ioAdapter->write(basename($fileName), $fileContent, 0666);

            } catch(Exception $e) {
                $this->_fault('not_created', Mage::helper('catalog')->__('Can\'t create image.'));
            }
        }elseif (isset($data['rename']) && isset($data['newname'])) {
            // Should rename the image                        
            $image = $product->getMediaGalleryImages()->getItemByColumnValue('file', $file);
            if (!$image) { return false; }
            $entity_id = $product->getId();
            $fullpath = $image->getFile();
            $path = substr($fullpath, 0, strrpos($fullpath,'/')+1);
            $seo_name = $data['newname'].'.jpg';      Mage::log($seo_name);      
            
            $folder = Mage::getBaseDir() . DS . 'media' . DS . 'catalog' . DS . 'product';
            rename($folder . $fullpath,$folder . $path. $seo_name);

            //Mage::log($folder . $fullpath); Mage::log($sql); 

            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');     
            
            $sql = "UPDATE catalog_product_entity_media_gallery SET value = '".$path.$seo_name."' WHERE entity_id = $entity_id and value = '$fullpath';";            
            $connection->query($sql);

            $sql = "UPDATE catalog_product_entity_varchar SET value = '".$path.$seo_name."' WHERE entity_id = $entity_id and value = '$fullpath';";            
            $connection->query($sql);
            return true;
        }

        $gallery->getBackend()->updateImage($product, $file, $data);

        if (isset($data['types']) && is_array($data['types'])) {
            $oldTypes = array();
            foreach ($product->getMediaAttributes() as $attribute) {
                if ($product->getData($attribute->getAttributeCode()) == $file) {
                     $oldTypes[] = $attribute->getAttributeCode();
                }
            }

            $clear = array_diff($oldTypes, $data['types']);

            if (count($clear) > 0) {
                $gallery->getBackend()->clearMediaAttribute($product, $clear);
            }

            $gallery->getBackend()->setMediaAttribute($product, $data['types'], $file);
        }

        try {
            $product->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_updated', $e->getMessage());
        }

        return true;
    }
}
