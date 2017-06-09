<?php

class FactoryX_ReviewNotification_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Function name
     *
     * what the function does
     *
     * @param (type) (name) about this param
     * @return (type) (name)
     */
    public function getDirectUrl($orderId) {
        //Mage::log(sprintf("%s->orderId=%s", __METHOD__, $orderId) );
        $order = Mage::getModel('sales/order')->load($orderId);
        $hashCode = md5($order->getIncrementId());
        $storeId = $order->getStoreId();
        //Mage::log(sprintf("%s->storeId=%s", __METHOD__, $storeId) );
        // Mage_Core_Model_Store
        $store = Mage::getModel('core/store')->load($storeId);
        $route = 'reviewnotification/front/postReviews';

        $params = array(
            '_nosid'            => true,
            '_secure'           => true,
            //'_forced_secure'    => true,
            '_store'            => $storeId,
            'security_key'      => $hashCode
        );
        //$url = Mage::getUrl($route, array('_store' =>  $storeId, 'security_key' => $hashCode));
        //$url = Mage::helper('adminhtml')->getUrl($route, $params);
        $url = Mage::getUrl($route, $params);
        //Mage::log(sprintf("%s->url=%s", __METHOD__, $url) );
        return $url;
    }

}