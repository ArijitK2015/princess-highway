<?php

/**
 * Class FactoryX_CampaignMonitor_Model_Newsletter_Observer
 */
class FactoryX_CampaignMonitor_Model_Newsletter_Observer
{
    /**
     *
     */
    public function redirectToSubscribe()
    {
        // We need to redirect with the email added to the URL
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $email = $customer->getEmail();
        $apiKey = Mage::helper('campaignmonitor')->getApiKey();
        // Generate URL
        $url = sprintf("%ssubscribe?email=%s&key=%s", Mage::getBaseUrl(), $email, md5($email.$apiKey));;
        // Redirect
        Mage::app()->getResponse()->setRedirect($url)->sendResponse();
    }
}