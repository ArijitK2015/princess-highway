<?php
/**
 * FactoryX_StoreLocator extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FactoryX
 * @package    FactoryX_StoreLocator
 * @copyright
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   FactoryX
 * @package    FactoryX_StoreLocator
 * @author
 */
class FactoryX_StoreLocator_Model_Location extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('ustorelocator/location');
    }

    public function loadByIpAddress($ip) {
        $this->_getResource()->loadByIpAddress($this, $ip);
        return $this;
    }

    /**
     * @return $this
     */
    public function fetchCoordinates()
    {
        $url = "https://maps.googleapis.com/maps/api/geocode/json";
        $url .= strpos($url, '?') !== false ? '&' : '?';
        $url .= 'address='.urlencode(preg_replace('#\r|\n#', ' ', $this->getAddress()))."&sensor=false";

        $cinit = curl_init();
        curl_setopt($cinit, CURLOPT_URL, $url);
        curl_setopt($cinit, CURLOPT_HEADER,0);
        curl_setopt($cinit, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
        curl_setopt($cinit, CURLOPT_RETURNTRANSFER, 1);
        usleep(100000);// sleep for 0.1 sec to try avoid too many requests per second to Google
        $response = curl_exec($cinit);
        if (!is_string($response) || empty($response)) {
            Mage::helper('ustorelocator')->log($response, Zend_Log::ERR);
            return $this;
        }
        $result = json_decode($response);
        if (strtolower($result->status) != 'ok') {
            //echo '<pre>'.$response.'</pre>';
            Mage::helper('ustorelocator')->log($result, Zend_Log::ERR);
            return $this;
        }
        $this->setLatitude($result->results[0]->geometry->location->lat)
            ->setLongitude($result->results[0]->geometry->location->lng);
        return $this;
    }

    protected function _beforeSave()
    {
        if (!$this->getAddress()) {
            $this->setAddress($this->getAddressDisplay());
        }

        $this->setAddress(str_replace(array("\n", "\r"), " ", $this->getAddress()));

        if (!(float)$this->getLongitude() || !(float)$this->getLatitude() || $this->getRecalculate()) {
            $this->fetchCoordinates();
        }

        parent::_beforeSave();
    }
}
