<?php
/**
 * Who:  Alvin Nguyen
 * When: 1/10/2014
 * Why:  
 */

class FactoryX_ProductPolice_Model_Observer{

    /**
     * @param $observer
     *
     * Observer to trim the image label
     */
    public function catalog_product_save_before($observer){
        if (!Mage::getStoreConfig('productpolice/options/enable'))
            return;

        $_helper = Mage::helper('factoryx_productpolice');
        $_product = $observer->getProduct();

        if ($_product->getTypeId() == 'configurable'){
            // Let's removing heading/trailing spaces
            $_helper->removeSpaces($_product);
        }
    }

    /**
     * @param $observer
     * @throws Exception
     *
     * Observer to run after catalog product save event to detect image label naming issue
     */
    public function catalog_product_save_after($observer){

        if (!Mage::getStoreConfig('productpolice/options/enable'))
            return;

        $_helper = Mage::helper('factoryx_productpolice');
        $_product = $observer->getProduct();

        if ($_product->getTypeId() == 'configurable'){
            // Let's check for faulty
            $missing_colours = $_helper->getMissingColours($_product);
            if (count($missing_colours) != 0){
                Mage::getSingleton('adminhtml/session')->addError($_helper->getMissingMessage($_product, $missing_colours));
                $_helper->logFaultyProduct($_product, $missing_colours);
            }else{
                $_helper->removeFaultyProductLog($_product);
            }
        }
    }

    /**
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function recheck(Mage_Cron_Model_Schedule $schedule = null){
        if ((Mage::getStoreConfig('productpolice/options/cron_enable')) || ($schedule == null)){
            // $schedule == null means it's being run from the sys config

            $_helper = Mage::helper('factoryx_productpolice');

            $_product_collection = Mage::getResourceModel('catalog/product_collection')
                ->addFieldToFilter('type_id','configurable')
                ->addFieldToFilter('status',1);
            $_backendModel = $_product_collection->getResource()->getAttribute('media_gallery')->getBackend();

            foreach ($_product_collection as $_product){
                $_backendModel->afterLoad($_product);
                $missing_colours = $_helper->getMissingColours($_product);
                if (count($missing_colours) != 0){
                    $_helper->logFaultyProduct($_product, $missing_colours);
                }else{
                    $_helper->removeFaultyProductLog($_product);
                }

            }

            // send report email
            $_helper->sendReportEmail();
        }
    }
}