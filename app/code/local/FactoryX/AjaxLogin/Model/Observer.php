<?php

/**
 * Class FactoryX_AjaxLogin_Model_Observer
 */
class FactoryX_AjaxLogin_Model_Observer
{
    /**
     * Add custom routes to the ReCAPTCHA module
     * @param Varien_Event_Observer $observer
     */
    public function addCustomRoute(Varien_Event_Observer $observer)
    {
        $routes = $observer->getEvent()->getRoutes();
        $routes->add(FactoryX_AjaxLogin_Helper_Data::AJAXLOGIN_INDEX_LOGIN_ROUTE, Mage::helper('ajaxlogin')->__('Ajax Login Popup'));
        $routes->add(FactoryX_AjaxLogin_Helper_Data::AJAXLOGIN_INDEX_CREATE_ROUTE, Mage::helper('ajaxlogin')->__('Ajax Registration Popup'));
        $routes->add(FactoryX_AjaxLogin_Helper_Data::AJAXLOGIN_INDEX_FORGOTPASSWORD_ROUTE, Mage::helper('ajaxlogin')->__('Ajax Forgot Password Popup'));
    }

    /**
     * Add the recaptcha blocks dynamically based on the recaptcha module presence and configuration
     * @param Varien_Event_Observer $observer
     */
    public function addRecaptchaBlocks(Varien_Event_Observer $observer)
    {
        if (Mage::helper('ajaxlogin')->isRecaptchaAllowedOnLogin()) {
            $layout = $observer->getLayout();
            $layout->getUpdate()->addHandle('ajaxlogin_recaptcha_login');
        }

        if (Mage::helper('ajaxlogin')->isRecaptchaAllowedOnRegister()) {
            $layout = $observer->getLayout();
            $layout->getUpdate()->addHandle('ajaxlogin_recaptcha_register');
        }

        if (Mage::helper('ajaxlogin')->isRecaptchaAllowedOnForgotPassword()) {
            $layout = $observer->getLayout();
            $layout->getUpdate()->addHandle('ajaxlogin_recaptcha_forgotpassword');
        }
    }
}