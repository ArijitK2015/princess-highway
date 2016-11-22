<?php
/**
 * Who:  Alvin Nguyen
 * When: 1/10/2014
 * Why:  
 */ 
class FactoryX_ProductPolice_Helper_Data extends Mage_Core_Helper_Abstract {
    /**
     * @param $product
     * @return array
     */
    public function getMissingColours($product){

        $media_gallery = $product->getData('media_gallery');

        $options = $product->getData('configurable_attributes_data');;
        if (!$options){
            // being loaded from a cron
            $options = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
        }

        $result = array();
        $label_option= array();
        $colours_from_label = array();
        $colours_from_product = array();

        foreach ($options as $option){
            if (preg_match('/colour/i',$option['label'])){
                foreach ($option['values'] as $value){
                    // there are label, default_label, store_label
                    $colours_from_product[] = strtolower(preg_replace('/\W|_/i',' ',$value['label']));
                }
                break;
            }
        }

        if (count($colours_from_product) == 0){
            // If there is no colours/options for selecting colour
            return $result;
        }

        foreach ($media_gallery["images"] as $image){
            $label = $image["label"];

            // Get the position of the _
            $position = strpos($label,"_");

            // If the is no _ we don't process
            if ($position == false)
                continue;

            // We only process front and swatch label
            if (!preg_match("/front/i",$label) && !preg_match("/swatch/i",$label))
                continue;

            // Get the colour from the label
            $colour = substr($label, ($position+1));

            if (isset($label_option[$colour])){
                $colours_from_label[] = strtolower(preg_replace('/\W|_/i',' ',$colour));
            }else{
                $label_option[$colour] = 1;
            }
        }

        foreach ($colours_from_product as $colour){
            if (!in_array($colour,$colours_from_label)){
                $result[] = $colour;
            }
        }

        return $result;
    }

    /**
     * @param $product
     * @param $missing_colours
     * @throws Exception
     */
    public function logFaultyProduct($product, $missing_colours){
        $_item = Mage::getModel('factoryx_productpolice/item');
        $_item->load($product->getId(),'product_id');
        $_item->setData('product_id',$product->getId());
        $_item->setData('error_message',$this->getMissingMessage($product, $missing_colours));;
        $_item->setData('created_at',strtotime('now'));
        $_item->setDataChanges(true);
        $_item->save();
        unset($_item);
    }

    /**
     * @param $product
     * @throws Exception
     */
    public function removeFaultyProductLog($product){
        $_item = Mage::getModel('factoryx_productpolice/item');
        $_item->load($product->getId(),'product_id');
        if ($_item->getId()){
            $_item->delete();
        }
        unset($_item);
    }

    /**
     * @param $product
     */
    public function removeSpaces(&$product){
        $media_gallery = $product->getData('media_gallery');
        $media_gallery_images = json_decode($media_gallery["images"],true);
        foreach($media_gallery_images as &$media_gallery_image){
            $media_gallery_image['label'] = trim($media_gallery_image['label']);
        }
        $media_gallery["images"] = json_encode($media_gallery_images);
        $product->setData('media_gallery',$media_gallery)->setDataChanges(true);
    }

    /**
     * @param $product
     * @param $missing_colours
     * @return string
     */
    public function getMissingMessage($product, $missing_colours){
        return $product->getSku()." - ".$product->getName()." has been saved but might not be working correctly as following colours are missing from image: ".implode(',',$missing_colours);
    }

    public function sendReportEmail(){
        $emails = explode(',',Mage::getStoreConfig('productpolice/options/emails'));
        $email_from = Mage::getStoreConfig('productpolice/options/email_from');
        $store_name = Mage::getStoreConfig('general/store_information/name');

        if (!$email_from || (count($emails) == 0)){
            return;
        }

        $collection = Mage::getResourceModel('factoryx_productpolice/item_collection');
        $collection->addProductData(array('name','sku','status'));

        $subject = "{$store_name} report for mis-configured products [".Mage::getModel('core/date')->date('Y-m-d H:i:s')."]";

        $body = "<table>";
        $body .= "<tr><td><b>ID</b></td><td><b>PID</b></td><td><b>SKU</b></td><td><b>Name</b></td><td><b>Status</b></td><td><b>Error</b></td></tr>";

        foreach($collection as $item){
            $body .= "<tr>";
            $body .= "<tr><td>{$item->getId()}</td><td>{$item->getData('product_id')}</td><td>{$item->getData('sku')}</td><td>{$item->getData('name')}</td><td>{$item->getData('status')}</td><td>{$item->getData('error_message')}</td></tr>";
            $body .= "</tr>";
        }

        $body .= "</table>";

        $body .= '<style>table {border-collapse: collapse;} table td {padding: 5px; border: 1px solid gray;}</style>';

        foreach ($emails as $email){
            $mail = Mage::getModel('core/email')
                ->setToEmail($email)
                ->setBody($body)
                ->setFromEmail($email_from)
                ->setFromName($store_name)
                ->setSubject($subject)
                ->setType('html');

            try {
                $mail->send();
            }
            catch (Exception $e) {
                Mage::log("There has been an error in ".__FILE__." with the message ".$e->getMessage());
            }
        }
    }

}