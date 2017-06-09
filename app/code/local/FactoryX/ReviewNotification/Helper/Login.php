<?php

class FactoryX_ReviewNotification_Helper_Login extends Mage_Core_Helper_Abstract {

    /**
     * Log customer
     * @param <type> $customerId
     */
    public function logCustomer($customerId)
    {
        $session = Mage::getSingleton('customer/session');
        $session->loginById($customerId);
    }

}