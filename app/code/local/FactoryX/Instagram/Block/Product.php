<?php

/**
 * Class FactoryX_Instagram_Block_Product
 */
class FactoryX_Instagram_Block_Product extends Mage_Core_Block_Template
{
    /**
     * @return null
     */
    public function getJSONData(){
        $_product_data = Mage::registry('current_product')->getData('instagram_hashtag');
        return (isset($_product_data['approved_images'])) ? $_product_data['approved_images'] : null;
    }

    /**
     * @return null
     */
    public function getTag(){
        $_product_data = Mage::registry('current_product')->getData('instagram_hashtag');
        return (isset($_product_data['hash_tag'])) ? $_product_data['hash_tag'] : null;
    }

    /**
     * @return mixed
     */
    public function getInstruction(){
        return Mage::getStoreConfig('factoryx_instagram/product_options/instruction');
    }

    /**
     * @return mixed
     */
    public function getExtraCSS(){
        return Mage::getStoreConfig('factoryx_instagram/product_options/style');
    }
}