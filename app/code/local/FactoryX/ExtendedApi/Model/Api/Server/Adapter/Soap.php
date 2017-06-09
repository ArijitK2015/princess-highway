<?php
/**
 *	replace https http
 *	$wsdlUrl = str_replace("https://", "http://", $wsdlUrl);
 */
class FactoryX_ExtendedApi_Model_Api_Server_Adapter_Soap
    extends Mage_Api_Model_Server_Adapter_Soap
{
    /**
     * Transform wsdl url if $_SERVER["PHP_AUTH_USER"] is set
     *
     * @param array
     * @param bool $withAuth
     * @return String
     */
    protected function getWsdlUrl($params = null, $withAuth = true)
    {
        Mage::log(sprintf("%s->params=%s", __METHOD__, print_r($params, true)) );
        
        $urlModel = Mage::getModel('core/url')
            ->setUseSession(false);

        $wsdlUrl = $params !== null
            ? $urlModel->getUrl('*/*/*', array('_current' => true, '_query' => $params))
            : $urlModel->getUrl('*/*/*');

        if ( $withAuth ) {
            $phpAuthUser = rawurlencode($this->getController()->getRequest()->getServer('PHP_AUTH_USER', false));
            $phpAuthPw = rawurlencode($this->getController()->getRequest()->getServer('PHP_AUTH_PW', false));
            $scheme = rawurlencode($this->getController()->getRequest()->getScheme());

            if ($phpAuthUser && $phpAuthPw) {
                $wsdlUrl = sprintf("%s://%s:%s@%s", $scheme, $phpAuthUser, $phpAuthPw,
                    str_replace($scheme . '://', '', $wsdlUrl));
            }
        }
        $wsdlUrl = str_replace("https://", "http://", $wsdlUrl);
        $wsdlUrl = preg_replace("/:\d{1,}/", ":80", $wsdlUrl);
        return $wsdlUrl;
    }
}
