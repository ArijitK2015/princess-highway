<?php

/**
 * Who:  Alvin Nguyen
 * When: 1/10/2014
 * Why:
 */
class FactoryX_ProductPolice_Helper_Data extends Mage_Core_Helper_Abstract
{
    function flat_str($str)
    {
        $str = strtolower(trim($str));
        return preg_replace("/\W|_/", "-", $str);
    }
    /**
     * @param $product
     * @return array
     */
    public function getColorImages(&$product,$types=array('swatch'))
    {
        $result = array();
        $_attributes = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
        if (empty($_attributes)) return $result;

        $_allImageLabels = array_column($product->getMediaGallery('images'), 'label');
        foreach ($_attributes as $_attribute) {
            $is_color = strpos($_attribute['attribute_code'], 'colour') !== FALSE;
            if ($is_color) {
                $options = $_attribute['values'];
                foreach ($options as $option) {
                    foreach ($types as $type) {
                        $type_key = array_search($type.'_' . $this->flat_str($option['label']), $_allImageLabels);
                        $result[$option['label']][$type]=$type_key;
                    }
                }
            }
        }
        return $result;
    }
    /**
     * @param $product
     * @return array
     */
    public function getMissingColours(&$product)
    {
        $result=array();
        $colorImages = $this->getColorImages($product,array('swatch','front'));
         foreach ($colorImages as $color=>$img_key) {
             if ($img_key['swatch']===false || $img_key['front']===false) $result[] = $color;
         }
         return $result;
    }
    /**
     * @param $product
     * @param $missing_colours
     * @throws Exception
     */
    public function logFaultyProduct($product, $missing_colours)
    {
        $_item = Mage::getModel('factoryx_productpolice/item');
        $_item->load($product->getId(), 'product_id');
        $_item->setData('product_id', $product->getId());
        $_item->setData('error_message', $this->getMissingMessage($product, $missing_colours));;
        $_item->setData('created_at', strtotime('now'));
        $_item->setDataChanges(true);
        $_item->save();
        unset($_item);
    }

    /**
     * @param $product
     * @throws Exception
     */
    public function removeFaultyProductLog($product)
    {
        $_item = Mage::getModel('factoryx_productpolice/item');
        $_item->load($product->getId(), 'product_id');
        if ($_item->getId()) {
            $_item->delete();
        }
        unset($_item);
    }

    /**
     * @param $product
     */
    public function removeSpaces(&$product)
    {
        $media_gallery = $product->getData('media_gallery');
        $media_gallery_images = json_decode($media_gallery["images"], true);
        foreach ($media_gallery_images as &$media_gallery_image) {
            $media_gallery_image['label'] = trim($media_gallery_image['label']);
        }
        $media_gallery["images"] = json_encode($media_gallery_images);
        $product->setData('media_gallery', $media_gallery)->setDataChanges(true);
    }
    /**
     * @param $product
     */
    public function fixMediaLabels()
    {
        $resource = Mage::getSingleton('core/resource');
        $conn = $resource->getConnection('core_write');
        $table = $resource->getTableName('catalog_product_entity_media_gallery_value');
        $sql = "UPDATE {$table}
                SET label=concat(trim(SUBSTRING_INDEX(label,'_',1)),
                '_',
                replace(trim(SUBSTRING_INDEX(SUBSTRING_INDEX(label,'_',2),'_',-1)), ' ','-'))
                WHERE label like '% %';";
        $conn->query($sql);
    }

    /**
     * @param $product
     * @param $missing_colours
     * @return string
     */
    public function getMissingMessage($product, $missing_colours)
    {
        return $product->getSku() . " - " . $product->getName() . " has been saved but might not be working correctly as following colours are missing from image: " . implode(',', $missing_colours);
    }

    public function sendReportEmail()
    {
        $emails = explode(',', Mage::getStoreConfig('productpolice/options/emails'));
        $email_from = Mage::getStoreConfig('productpolice/options/email_from');
        $store_name = Mage::getStoreConfig('general/store_information/name');

        if (!$email_from || (count($emails) == 0)) {
            return;
        }

        $collection = Mage::getResourceModel('factoryx_productpolice/item_collection');
        $collection->addProductData(array('name', 'sku', 'status'));

        $subject = "{$store_name} report for mis-configured products [" . Mage::getModel('core/date')->date('Y-m-d H:i:s') . "]";

        $body = "<table>";
        $body .= "<tr><td><b>ID</b></td><td><b>PID</b></td><td><b>SKU</b></td><td><b>Name</b></td><td><b>Status</b></td><td><b>Error</b></td></tr>";

        foreach ($collection as $item) {
            $body .= "<tr>";
            $body .= "<tr><td>{$item->getId()}</td><td>{$item->getData('product_id')}</td><td>{$item->getData('sku')}</td><td>{$item->getData('name')}</td><td>{$item->getData('status')}</td><td>{$item->getData('error_message')}</td></tr>";
            $body .= "</tr>";
        }

        $body .= "</table>";

        $body .= '<style>table {border-collapse: collapse;} table td {padding: 5px; border: 1px solid gray;}</style>';

        foreach ($emails as $email) {
            $mail = Mage::getModel('core/email')
                ->setToEmail($email)
                ->setBody($body)
                ->setFromEmail($email_from)
                ->setFromName($store_name)
                ->setSubject($subject)
                ->setType('html');

            try {
                $mail->send();
            } catch (Exception $e) {
                Mage::log("There has been an error in " . __FILE__ . " with the message " . $e->getMessage());
            }
        }
    }

}