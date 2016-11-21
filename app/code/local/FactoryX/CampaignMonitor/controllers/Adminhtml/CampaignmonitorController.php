<?php
/**
 * Class FactoryX_CampaignMonitor_Adminhtml_CampaignmonitorController
 * Admin controller used for the OAuth authentication
 */
class FactoryX_CampaignMonitor_Adminhtml_CampaignmonitorController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/config/newsletter');
    }

    /**
     *  Disable secret key to be able to use the callback
     */
    public function preDispatch()
    {
        Mage::getSingleton('adminhtml/url')->turnOffSecretKey();
        parent::preDispatch();
    }

    /**
     *  Redirect to the auth Url
     */
    public function indexAction()
    {
        $this->_redirectUrl($this->_getAuthUrl());
    }

    /**
     *  Trigerred when clicking on the refresh token button
     */
    public function refreshtokenAction()
    {
        // Refresh the token
        Mage::helper('campaignmonitor/cm')->refreshToken();
        // Generate the redirect URL
        $redirectUrl = Mage::helper('campaignmonitor')->getAdminConfigSectionUrl();
        // Redirect
        $this->_redirectUrl($redirectUrl);
    }

    /**
     * Callback action when coming back from OAuth
     */
    public function callbackAction()
    {
        // Get the code
        $code = $this->getRequest()->getParam('code');
        // No need to get the state as not used
        //$state = $this->getRequest()->getParam('state');
        // Get access token from the code
        $response = $this->_getAccessToken($code);
        if ($response)
        {
            // Store the response in session
            $session = Mage::getModel('core/session');
            $session->setData(FactoryX_CampaignMonitor_Helper_Cm::CAMPAIGNMONITOR_SESSION_DATA_KEY, $response);
            // Save the response in the config
            Mage::getConfig()->saveConfig(FactoryX_CampaignMonitor_Helper_Cm::CAMPAIGNMONITOR_CONFIG_DATA_KEY, serialize($response), 'default', 0);
        }
        else
        {
            // Error
            Mage::getSingleton('adminhtml/session')->addError("Error: cannot retrieve access token from OAuth");
        }
        // Generate redirect URL
        $redirectUrl = Mage::helper('campaignmonitor')->getAdminConfigSectionUrl();
        // Redirect
        $this->_redirectUrl($redirectUrl);
    }

    /**
     * Get the access token from the code
     * @param $code
     * @return bool|mixed
     */
    protected function _getAccessToken($code)
    {
        return Mage::helper('campaignmonitor/cm')->getAccessToken($this->_getAuthRedirectUri(),$code);
    }

    /**
     * Get url for authentification on OAuth
     * @return string
     */
    protected function _getAuthUrl()
    {
        return Mage::helper('campaignmonitor/cm')->getAuthUrl($this->_getAuthRedirectUri());
    }

    /**
     * Get the redirect URL
     * @return mixed
     * @throws Mage_Core_Exception
     */
    protected function _getAuthRedirectUri()
    {
        return str_replace('http://','https://',Mage::app()->getStore(1)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK)."campaignmonitor/auth/index");
    }

}
