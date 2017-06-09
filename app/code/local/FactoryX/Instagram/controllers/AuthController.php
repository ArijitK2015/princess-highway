<?php
/**
 * iKantam
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade InstagramConnect to newer
 * versions in the future.
 *
 * @category    Ikantam
 * @package     FactoryX_Instagram
 * @copyright   Copyright (c) 2012 iKantam LLC (http://www.factoryx.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class FactoryX_Instagram_AuthController extends Mage_Core_Controller_Front_Action
{

    /*
    @TODO check isAdmin login
    */
    public function indexAction() {

        $adminRoute = "adminhtml/instagram_auth/callback";
        
        $params = array(
            'code' => $this->getRequest()->getQuery('code')
        );

        $adminUrl = Mage::helper("adminhtml")->getUrl($adminRoute, $params);
        //Mage::helper('instagram')->log(sprintf("%s->adminUrl=%s", __METHOD__, $adminUrl), Zend_Log::DEBUG );

        $adminBaseUrl = Mage::getStoreConfig('web/secure/base_url', 0);
        //Mage::helper('instagram')->log(sprintf("%s->baseUrl=%s", __METHOD__, $baseUrl), Zend_Log::DEBUG );

        if (strpos($adminUrl, $adminBaseUrl) === false) {
            $baseUrl = Mage::getStoreConfig('web/secure/base_url');
            //Mage::helper('instagram')->log(sprintf("%s->baseUrl=%s", __METHOD__, $baseUrl), Zend_Log::DEBUG );
            // replace base url with the admin base url
            if (strpos($adminUrl, $baseUrl) !== false) {
                Mage::helper('instagram')->log(sprintf("%s->replace=%s|%s", __METHOD__, $baseUrl, $adminBaseUrl), Zend_Log::DEBUG );
                $adminUrl = str_replace($baseUrl, $adminBaseUrl, $adminUrl);
            }
        }
        Mage::helper('instagram')->log(sprintf("%s->adminUrl=%s", __METHOD__, $adminUrl), Zend_Log::DEBUG );
        $this->_redirectUrl($adminUrl);
    }

}
