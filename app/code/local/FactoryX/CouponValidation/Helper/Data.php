<?php

/**
 * Class FactoryX_CouponValidation_Helper_Data
 */
class FactoryX_CouponValidation_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $logFileName = 'factoryx_couponvalidation.log';

    protected $_allowedIpAddresses = array();

    /**
     * Remote address cache
     *
     * @var string
     */
    protected $_remoteAddr;    

    /**
     * support comma delimited lists of ip addresses such as ip1,ip2
     *
     * @return array of ip addresses
     */
    private function _getAllowedIpAddress() {
        if (!empty(($this->_allowedIpAddresses))) {
            return $this->_allowedIpAddresses;
        }        
        $collection = Mage::getResourceModel('ustorelocator/location_collection');
        foreach($collection as $store) {
            $ipAddress = $store->getIpAddress();
            //$this->log(sprintf("%s->ipAddress: %s: %s", __METHOD__, $store->getStoreCode(), $ipAddress));
            if (filter_var($ipAddress, FILTER_VALIDATE_IP)) {
                //$this->log(sprintf("%s->ipAddress: %s", __METHOD__, $ipAddress));
                $this->_allowedIpAddresses[] = $ipAddress;
            }
            // comma delimited lists of ip addresses
            elseif(preg_match('/\b((25[0-5]|2[0-4]\d|[01]?\d{1,2})\.){3}(25[0-5]|2[0-4]\d|[01]?\d{1,2})\b/', $ipAddress)) {
                $ipAddresses = preg_split('/\,/', $ipAddress);
                foreach($ipAddresses as $ipAddress) {
                    //$this->log(sprintf("%s->ipAddress: %s", __METHOD__, $ipAddress));
                    $this->_allowedIpAddresses[] = $ipAddress;
                }   
            }
            elseif(!empty($ipAddress)) {
                $this->log(sprintf("invalid ip address '%s' in store '%s'", $ipAddress, $store->getStoreCode()), Zend_Log::WARN);
            }
        }
        return $this->_allowedIpAddresses;
    }

    /**
     * isAllowed
     *
     * @param string ip address
     * @return bool is allowed
     */
    public function isAllowed($ipAddress = null) {        
        //$this->log(sprintf("%s->getAllowedIpAddress: %s", __METHOD__, print_r($this->_getAllowedIpAddress(), true)));        
        $isAllowed = in_array($ipAddress, $this->_getAllowedIpAddress());
        //$this->log(sprintf("%s->ipAddress: %s|%d", __METHOD__, $ipAddress, $isAllowed));        
        return $isAllowed;
    }

    /**
     * Retrieve Client Remote Address, with web proxy support
     *
     * taken from Mage_Core_Helper_Http::getRemoteAddr
     *
     * @param bool $ipToLong converting IP to long format
     * @return string IPv4|long
     */
    public function getRemoteAddr($ipToLong = false)
    {           
        if (is_null($this->_remoteAddr)) {
            $this->_remoteAddr = $this->_getIpAddress();
        }
        if (!$this->_remoteAddr) {
            return false;
        }
        return $ipToLong ? inet_pton($this->_remoteAddr) : $this->_remoteAddr;
    }

    /**
     * _getIpAddress
     */
    private function _getIpAddress() {
        $retVal = null;
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
            //$this->log(sprintf("%s->_SERVER['%s']=%s", __METHOD__, $key, $_SERVER[$key]));
            if (array_key_exists($key, $_SERVER) === true) {
                // fix stuff like 192.168.120.50, 203.214.69.30
                $data = str_replace(' ', '', $_SERVER[$key]);
                foreach (explode(',', $data) as $ip) {
                    $ip = trim($ip); // just to be safe
                    //$this->log(sprintf("%s->_SERVER['%s']=org:%s|fix:%s|ip:%s|filter:%d", __METHOD__, $key, $_SERVER[$key], $data, $ip, filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)));
                    /*
                    FILTER_FLAG_NO_PRIV_RANGE Fails validation for the following private IPv4 ranges: 10.0.0.0/8, 172.16.0.0/12 and 192.168.0.0/16.
                    this fails for the fx head office 192.168.120.50
                    */
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    //if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE) !== false) {
                        $retVal = $ip;
                        break 2;
                    }
                }
            }
        }
        //$this->log(sprintf("%s->%s: %s", __METHOD__, $retVal));
        return $retVal;
    }

    public function hashValidationEnabled()
    {
        return Mage::getStoreConfigFlag('couponvalidation/options/hash');
    }

	/**
	 * Log data
	 * @param string|object|array data to log
	 */
	public function log($data, $level = Zend_Log::DEBUG)
	{
		Mage::log($data, $level, $this->logFileName);
	}
    
}