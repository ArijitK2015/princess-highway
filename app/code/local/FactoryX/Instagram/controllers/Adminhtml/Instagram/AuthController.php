<?php
class FactoryX_Instagram_Adminhtml_Instagram_AuthController extends Mage_Adminhtml_Controller_Action
{
    const INSTAGRAM_AUTH_URL = 'https://api.instagram.com/oauth/authorize/';
    const INSTAGRAM_ACCESSS_TOKEN_URL = 'https://api.instagram.com/oauth/access_token';

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/config/factoryx_instagram');
    }

    public function preDispatch()
    {
        Mage::getSingleton('adminhtml/url')->turnOffSecretKey();
        parent::preDispatch();
    }

	public function indexAction()
    {
        $this->_redirectUrl($this->_getAuthUrl());
    }

    public function callbackAction()
    {
        $code = $this->getRequest()->getParam('code');
        Mage::helper('instagram')->log(sprintf("%s->code=%s", __METHOD__, $code), Zend_Log::DEBUG );

        $response = $this->_getAccessToken($code);
        $responseObject = json_decode($response);
        Mage::helper('instagram')->log(sprintf("%s->response=%s", __METHOD__, print_r($responseObject, true)), Zend_Log::DEBUG );

        /** @var $session Mage_Core_Model_Session  */
        $session = Mage::getModel('core/session');
        $session->setData(
            FactoryX_Instagram_Model_Instagramauth::INSTAGRAM_SESSION_DATA_KEY,
            $responseObject
        );
        Mage::getConfig()->saveConfig(
            FactoryX_Instagram_Model_Instagramauth::INSTAGRAM_CONFIG_DATA_KEY,
            serialize($responseObject),
            'default',
            0
        );
        $redirectUrl = Mage::helper('instagram')->getAdminConfigSectionUrl();
        Mage::helper('instagram')->log(sprintf("%s->redirectUrl=%s", __METHOD__, $redirectUrl), Zend_Log::DEBUG );
        $this->_redirectUrl($redirectUrl);
    }

    /**
     * @param $code
     * @return mixed
     */
    protected function _getAccessToken($code)
    {
        $postParams = $this->_getInstagamHelper()->buildUrl(
            null,
            array(
                'client_id' => $this->_getClientId(),
                'client_secret' => $this->_getClientSecret(),
                'grant_type'    => 'authorization_code',
                'redirect_uri'  => $this->_getAuthRedirectUri(),
                'code'          => $code
            )
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::INSTAGRAM_ACCESSS_TOKEN_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        if (Mage::getStoreConfig('factoryx_instagram/module_options/proxy')) {
            Mage::helper('instagram')->log(sprintf("%s->proxy=%s", __METHOD__, Mage::getStoreConfig('factoryx_instagram/module_options/proxy')), Zend_Log::DEBUG );
            curl_setopt($ch, CURLOPT_PROXY, Mage::getStoreConfig('factoryx_instagram/module_options/proxy'));
        }
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;

    }

    /**
     * Get url for authentification on Instagram
     * @return string
     */
    protected function _getAuthUrl()
    {
        /** @var $url  */
        $url = $this->_getInstagamHelper()->buildUrl(
             self::INSTAGRAM_AUTH_URL,
             array(
                 'client_id'        => $this->_getClientId(),
                 'redirect_uri'     => $this->_getAuthRedirectUri(),
                 'response_type'    => 'code',
                 'scope'            => Mage::helper('instagram')->getScopes()
             )
        );
        Mage::helper('instagram')->log(sprintf("%s->url=%s", __METHOD__, $url), Zend_Log::DEBUG );
        return $url;
    }

    /**
     * @return mixed
     */
    protected function _getAuthRedirectUri()
    {
        return Mage::getUrl('instagram/auth');
    }

    /**
     * @return mixed
     */
    protected function _getClientId()
    {
        return Mage::getStoreConfig('factoryx_instagram/module_options/client_id');
    }


    /**
     * @return mixed
     */
    protected function _getClientSecret()
    {
        return Mage::getStoreConfig('factoryx_instagram/module_options/client_secret');
    }

    /**
     * @return FactoryX_Instagram_Helper_Data
     */
    protected function _getInstagamHelper()
    {
        return Mage::helper('instagram');
    }

}
