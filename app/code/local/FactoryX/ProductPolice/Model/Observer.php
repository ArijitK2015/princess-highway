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
            ini_set('max_execution_time', 300);
            // $schedule == null means it's being run from the sys config

            $_helper = Mage::helper('factoryx_productpolice');
            $_helper->fixMediaLabels();

            $last_scan_time = Mage::getStoreConfig('productpolice/system/last_scan_time');
            if(empty($last_scan_time)) $last_scan_time = 0;

            $faulty_ids = Mage::getModel('factoryx_productpolice/item')->getCollection()->getColumnValues('product_id');

            $defaultStoreId = Mage::app()
                ->getWebsite(true)
                ->getDefaultGroup()
                ->getDefaultStoreId();

            $initialEnvironmentInfo = Mage::getSingleton('core/app_emulation')->startEnvironmentEmulation($defaultStoreId);

            $_product_collection = Mage::getResourceModel('catalog/product_collection')
                ->addFieldToFilter('type_id','configurable')
                ->addFieldToFilter('status',1)
                ->addFieldToFilter('updated_at', array('gt' => $last_scan_time));

            $_backendModel = $_product_collection->getResource()->getAttribute('media_gallery')->getBackend();

            foreach ($_product_collection as $_product){
                $_backendModel->afterLoad($_product);
                $missing_colours = $_helper->getMissingColours($_product);
                if (count($missing_colours) != 0){
                    $_helper->logFaultyProduct($_product, $missing_colours);
                }else{
                    if(in_array($_product->getId(),$faulty_ids)) $_helper->removeFaultyProductLog($_product);
                }
            }

            Mage::getSingleton('core/app_emulation')->stopEnvironmentEmulation($initialEnvironmentInfo);

            // send report email
            $_helper->sendReportEmail();

            Mage::getModel('core/config')->saveConfig('productpolice/system/last_scan_time', Varien_Date::now());

        }
    }
}