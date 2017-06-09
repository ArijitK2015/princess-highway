<?php

/**
 * Class FactoryX_PromoRestriction_Helper_Data
 */
class FactoryX_PromoRestriction_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @var string
     */
    protected $logFileName = 'factoryx_promorestriction.log';

    /**
     * @var
     */
    protected $_remoteAddr;

    /**
     * Log data
     * @param string|object|array data to log
     */
    public function log($data, $level = Zend_Log::DEBUG)
    {
        Mage::log($data, $level, $this->logFileName);
    }

    /**
     * Get list of stores with related IP addresses
     * @return array
     */
    public function getStores()
    {
        $stores = Mage::getResourceModel('ustorelocator/location_collection')
            ->addFieldToSelect(['store_code', 'title'])
            ->addFieldToFilter('ip_address', ['notnull' =>  true]);

        $options = [];

        foreach($stores as $store) {
            $options[] = [
                'value'     =>  $store->getStoreCode(),
                'label'     =>  sprintf("%s - %s", $store->getStoreCode(), $store->getTitle())
            ];
        }

        return $options;
    }

    /**
     * extractEmailAddresses
     *
     * @param $string string
     * @return array emails
     */
    public static function extractEmailAddresses($string) {
        $emails = array();
        $string = str_replace("\r\n",' ',$string);
        $string = str_replace("\n",' ',$string);
        foreach(preg_split('/ /', $string) as $token) {
            $email = filter_var($token, FILTER_VALIDATE_EMAIL);
            if ($email !== false) {
                $emails[] = $email;
            }
        }
        return $emails;
    }

    /**
     * extractEmailAddress
     *
     * @param $string string
     * @return string email
     */
    public static function extractEmailAddress($string) {
        $email = null;
        $emails = self::extractEmailAddresses($string);
        if (is_array($emails) && $emails && $emails[0]) {
            $email = $emails[0];
        }
        return $email;
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

}