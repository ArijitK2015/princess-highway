<?php
/**
 * xmlfeed data helper
 *
 * @category   FactoryX
 * @package    FactoryX_XMLFeed
 */
class FactoryX_XMLFeed_Helper_Data extends Mage_Core_Helper_Abstract {

	protected $logFileName = 'factoryx_xmlfeed.log';
	
    /**
     * Authenticate customer on frontend
     *
     */
    public function authFrontend() {
        $session = Mage::getSingleton('rss/session');
        if ($session->isCustomerLoggedIn()) {
            return;
        }
        list($username, $password) = $this->authValidate();
        $customer = Mage::getModel('customer/customer')->authenticate($username, $password);
        if ($customer && $customer->getId()) {
            Mage::getSingleton('rss/session')->settCustomer($customer);
        }
        else {
            $this->authFailed();
        }
    }

    /**
     * Validate Authenticate
     *
     * @param array $headers
     * @return array
     */
    public function authValidate($headers=null)
    {
        $userPass = Mage::helper('core/http')->authValidate($headers);
        return $userPass;
    }

    /**
     * Send authenticate failed headers
     *
     */
    public function authFailed() {
        Mage::helper('core/http')->authFailed();
    }
	
	/**
	 * Log data
	 * @param string|object|array data to log
	 */
	public function log($data) 
	{
		Mage::log($data, null, $this->logFileName);
	}
}
