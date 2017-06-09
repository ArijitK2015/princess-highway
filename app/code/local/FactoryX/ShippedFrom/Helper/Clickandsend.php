<?php

/**
 * Class FactoryX_ShippedFrom_Helper_Clickandsend
 */
class FactoryX_ShippedFrom_Helper_Clickandsend extends Mage_Core_Helper_Abstract
{
    const CLICK_AND_SEND_MAXLEN_ADDRESS                     = 50;
    const XML_PATH_CLICK_AND_SEND_ENABLED                   = 'shippedfrom/clickandsend/active';
    const XML_PATH_CLICK_AND_SEND_FILTER_SHIPPING_METHODS   = 'shippedfrom/clickandsend/filter';
    const XML_PATH_CLICK_AND_SEND_EXPORT_ALL                = 'shippedfrom/clickandsend/export_all';
    const XML_PATH_CLICK_AND_SEND_SERVICE_CODE              = 'shippedfrom/clickandsend/service_code';
    const XML_PATH_CLICK_AND_SEND_ARTICLE_TYPE              = 'shippedfrom/clickandsend/article_type';
    const XML_PATH_CLICK_AND_SEND_SENDER_TRACK_ADVICE       = 'shippedfrom/clickandsend/sender_track_advice';
    const XML_PATH_CLICK_AND_SEND_RECEIVER_TRACK_ADVICE     = 'shippedfrom/clickandsend/receiver_track_advice';

    /**
     * @return bool
     */
    public function isClickAndSendEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CLICK_AND_SEND_ENABLED);
    }

    /**
     * @return bool
     */
    public function isFilterShippingMethods()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CLICK_AND_SEND_FILTER_SHIPPING_METHODS);
    }

    /**
     * @return bool
     */
    public function isExportAll()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CLICK_AND_SEND_EXPORT_ALL);
    }

    /**
     * @return string
     */
    public function getServiceCode()
    {
        return Mage::getStoreConfig(self::XML_PATH_CLICK_AND_SEND_SERVICE_CODE);
    }

    /**
     * @return string
     */
    public function getSenderTrackAdvice()
    {
        return ((Mage::getStoreConfig(self::XML_PATH_CLICK_AND_SEND_SENDER_TRACK_ADVICE)) ? 'Y' : 'N');
    }

    /**
     * @return string
     */
    public function getReceiverTrackAdvice()
    {
        return ((Mage::getStoreConfig(self::XML_PATH_CLICK_AND_SEND_RECEIVER_TRACK_ADVICE)) ? 'Y' : 'N');
    }

    /**
     * Receiver Company Name
     *
     * @param $address
     * @return string
     */
    public function getReceiverCompanyName($address)
    {
        return self::cleanString($address->getCompany());
    }

    /**
     * Receiver Name
     *
     * @param $address
     * @return string
     */
    public function getReceiverName($address)
    {
        return self::cleanString($address->getName());
    }

    /**
     * converts a Magento address into an 3 element array, max 50 chars in length
     *
     * System -> Configuration -> Customers -> Customer Configuration -> Name and Adress Options
     * Number of Lines in a Street Address
     * customer_address_street_lines
     * customer/address/street_lines
     * @param $address
     * @return array string
     */
    public function getAddress($address)
    {
        $aAddress = array();
        /*
        // before I knew of full address
        // $this->helper('customer/address')->getStreetLines();
        $lines = Mage::getStoreConfig('customer/address/street_lines');
        if (!$lines) {
            $lines = 2;
        }
        $fullAddress = "";
        for($i = 0; $i < $lines; $i++) {
            if ($address->getStreet($i)) {
                $fullAddress .= sprintf("%s%s", (strlen($fullAddress) ? " " : ""), $address->getStreet($i));
            }
        }
        */
        $fullAddress = $address->getStreetFull();
        // replace multiple spaces with a single
        $fullAddress = preg_replace("/\s+/", ' ', $fullAddress);
        
        // split on space
        $addrSplit = preg_split("/\s/", $fullAddress);
        $i = 0;
        foreach ($addrSplit as $part) {
            if (!array_key_exists($i, $aAddress)) {
                $aAddress[] = "";
            }

            $newLine = sprintf("%s%s%s", $aAddress[$i], (strlen($aAddress[$i]) ? " " : ""), $part);
            if (strlen($newLine) > self::CLICK_AND_SEND_MAXLEN_ADDRESS) {
                $i++;
                $aAddress[$i] = sprintf("%s", $part);
            } else {
                $aAddress[$i] .= sprintf("%s%s", (strlen($aAddress[$i]) ? " " : ""), $part);
            }
        }

        return $aAddress;
    }

    /**
     * @param $str
     * @internal param string $string return preg_replace("/[^ \w]+/", "", $str);*
     * return preg_replace("/[^ \w]+/", "", $str);
     * @return string
     */
    protected static function cleanString($str)
    {
        // replace ampersands with 'n'
        $str = preg_replace("/&/", 'n', $str);
        // replace non alpha numerics
        $str = preg_replace("/[^A-Za-z0-9 ]/", '', $str);
        // replace multiple spaces with a single
        return preg_replace("/\s+/", ' ', $str);
    }

    /**
     * @return string
     */
    public function getArticleType()
    {
        return Mage::getStoreConfig(self::XML_PATH_CLICK_AND_SEND_ARTICLE_TYPE);
    }
    
    /**
     * Returns shipping method service options not supported by Click & Send.
     *
     * @return array
     */
    /* 
    public function getDisallowedServiceOptions()
    {
        return array(
            ServiceOption::AUS_SERVICE_OPTION_SIGNATURE_ON_DELIVERY,
            ServiceOption::INTL_SERVICE_OPTION_EXTRA_COVER,
            ServiceOption::INTL_SERVICE_OPTION_PICKUP_METRO
        );
    }
    */
    
    /**
     * Returns shipping method service codes not supported by Click & Send.
     *
     * @return array
     */
     
    /* 
    public function getDisallowedServiceCodes()
    {
        return array(
            ServiceCode::AUS_PARCEL_COURIER,
            ServiceCode::AUS_PARCEL_COURIER_SATCHEL_MEDIUM,
            ServiceCode::INTL_SERVICE_SEA_MAIL
        );
    }
    */

    /**
     * Get the attribute value for a product, e.g. its length attribute. If the
     * order only has one item and we've set which product attribute we want to
     * to get the attribute value from, use that product attribute. For all
     * other cases, because we can't assume the dimensions of the order, just
     * use the default config setting.
     *
     * @param $order
     * @param $attribute
     * @return string
     */
    public function getAttribute($order, $attribute)
    {
        $items = $this->getAllSimpleItems($order);
        if (count($items) == 1) {
            $config = sprintf("carriers/australiapost/%s_attribute", $attribute);
            $attributeCode = Mage::getStoreConfig($config);
            if (empty($attributeCode)) {
                $config = sprintf("carriers/australiapost/default_%s", $attribute);
                return Mage::getStoreConfig($config);
            }

            $_attribute = $items[0]->getData($attributeCode);
            if (empty($_attribute)) {
                $config = sprintf("carriers/australiapost/default_%s", $attribute);
                return Mage::getStoreConfig($config);
            }

            return $_attribute;
        } else {
            $config = sprintf("carriers/australiapost/default_%s", $attribute);
            return Mage::getStoreConfig($config);
        }
    }

    /**
     * Get all the simple items in an order.
     *
     * @param FactoryX_Sales_Model_Order|Mage_Shipping_Model_Rate_Request $order
     * @return array
*/
    //public function getAllSimpleItems(Mage_Shipping_Model_Rate_Request $order)
    public function getAllSimpleItems(FactoryX_Sales_Model_Order $order)
    {
        $items = array();
        foreach ($order->getAllItems() as $item) {
            if ($item->getProductType() == 'simple') {
                $items[] = $item;
            }
        }

        return $items;
    }
    
    
}
