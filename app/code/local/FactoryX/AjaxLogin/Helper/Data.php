<?php

/**
 * Class FactoryX_AjaxLogin_Helper_Data
 */
class FactoryX_AjaxLogin_Helper_Data extends Mage_Core_Helper_Abstract {

    protected $logFileName = 'factoryx_ajaxlogin.log';

    /**
     * AJAX Login Route
     */
    const AJAXLOGIN_INDEX_LOGIN_ROUTE = 'ajaxlogin_index_login';

    /**
     * AJAX Register Route
     */
    const AJAXLOGIN_INDEX_CREATE_ROUTE = 'ajaxlogin_index_create';

    /**
     * AJAX Forgot Password Route
     */
    const AJAXLOGIN_INDEX_FORGOTPASSWORD_ROUTE = 'ajaxlogin_index_forgotpassword';

    /**
     * Log data
     * @param string|object|array data to log
     */
    public function log($data)
    {
        Mage::log($data, null, $this->logFileName);
    }

    /**
     * @return bool
     */
    public function isRecaptchaAllowedOnLogin()
    {
        return ((string)Mage::getConfig()->getModuleConfig('Studioforty9_Recaptcha')->active == 'true' && Mage::helper('studioforty9_recaptcha')->isAllowed(self::AJAXLOGIN_INDEX_LOGIN_ROUTE));
    }

    /**
     * @return bool
     */
    public function isRecaptchaAllowedOnRegister()
    {
        return ((string)Mage::getConfig()->getModuleConfig('Studioforty9_Recaptcha')->active == 'true' && Mage::helper('studioforty9_recaptcha')->isAllowed(self::AJAXLOGIN_INDEX_CREATE_ROUTE));
    }

    /**
     * @return bool
     */
    public function isRecaptchaAllowedOnForgotPassword()
    {
        return ((string)Mage::getConfig()->getModuleConfig('Studioforty9_Recaptcha')->active == 'true' && Mage::helper('studioforty9_recaptcha')->isAllowed(self::AJAXLOGIN_INDEX_FORGOTPASSWORD_ROUTE));
    }

}