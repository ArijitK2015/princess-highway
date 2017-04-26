<?php

/**
 * Class FactoryX_ShippedFrom_Helper_Auspost
 */
class FactoryX_ShippedFrom_Helper_Auspost extends Mage_Core_Helper_Abstract
{
    /**
     * Enabled configuration path
     */
    const XML_CONFIG_IS_ENABLED = "shippedfrom/auspost/enable";

    /**
     * @deprecated since 1.5.0
     * Store Id / Account No Mapping configuration path
     */
    const XML_CONFIG_MAPPING = "shippedfrom/auspost/mapping";

    /**
     * State / Account No Mapping configuration path
     */
    const XML_CONFIG_STATE_MAPPING = "shippedfrom/auspost/state_mapping";

    /**
     * Label Layout configuration path
     */
    const XML_CONFIG_LABEL_LAYOUT = "shippedfrom/auspost/label_layout";

    /**
     * Label Branded configuration path
     */
    const XML_CONFIG_LABEL_BRANDED = "shippedfrom/auspost/label_branded";

    /**
     * Label Left Offset configuration path
     */
    const XML_CONFIG_LABEL_LEFT_OFFSET = "shippedfrom/auspost/label_left_offset";

    /**
     * Label TOP Offset configuration path
     */
    const XML_CONFIG_LABEL_TOP_OFFSET = "shippedfrom/auspost/label_top_offset";

    /**
     * Global API Key configuration path
     */
    const XML_CONFIG_GLOBAL_API_KEY = "shippedfrom/auspost/api_key";

    /**
     * Global API Password configuration path
     */
    const XML_CONFIG_GLOBAL_API_PWD = "shippedfrom/auspost/api_pwd";

    /**
     * Global Account Number configuration path
     */
    const XML_CONFIG_GLOBAL_ACCOUNT_NO = "shippedfrom/auspost/account_no";

    /**
     * Global Dev API Key configuration path
     */
    const XML_CONFIG_GLOBAL_DEV_API_KEY = "shippedfrom/auspost/dev_api_key";

    /**
     * Global Dev API Password configuration path
     */
    const XML_CONFIG_GLOBAL_DEV_API_PWD = "shippedfrom/auspost/dev_api_pwd";

    /**
     * Global Dev Account Number configuration path
     */
    const XML_CONFIG_GLOBAL_DEV_ACCOUNT_NO = "shippedfrom/auspost/dev_account_no";

    /**
     * Global Dev Mode flag configuration path
     */
    const XML_CONFIG_IS_DEV_MODE = "shippedfrom/auspost/dev_mode";

    /**
     * Default Free Shipping Product configuration path
     */
    const XML_CONFIG_DEFAULT_FREE_SHIPPING_PRODUCT = "shippedfrom/auspost/free_shipping_default_product";

    /**
     * Default Ebay Product configuration path
     */
    const XML_CONFIG_DEFAULT_EBAY_PRODUCT = "shippedfrom/auspost/ebay_shipping_default_product";

    /**
     * Separate Article Items flag configuration path
     */
    const XML_CONFIG_SEPARATE_ARTICLE_ITEMS = "shippedfrom/auspost/separate_article_items";

    /**
     * Charge to master flag configuration path
     */
    const XML_CONFIG_CHARGE_TO_MASTER = "shippedfrom/auspost/charge_to_master";

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_CONFIG_IS_ENABLED);
    }

    /**
     * @return bool
     */
    public function isDevMode()
    {
        return Mage::getStoreConfigFlag(self::XML_CONFIG_IS_DEV_MODE);
    }

    /**
     * @deprecated since 1.5.0
     * @return mixed
     */
    public function getMapping()
    {
        return Mage::getStoreConfig(self::XML_CONFIG_MAPPING);
    }

    /**
     * @return mixed
     */
    public function getStateMapping()
    {
        $stateMappings = array();
        if (!empty(Mage::getStoreConfig(self::XML_CONFIG_STATE_MAPPING))
            && self::isSerialized(Mage::getStoreConfig(self::XML_CONFIG_STATE_MAPPING))) {
            $stateMappings = Mage::helper('core/unserializeArray')
                ->unserialize(Mage::getStoreConfig(self::XML_CONFIG_STATE_MAPPING));
        }

        return $stateMappings;
    }

    /**
     * @return mixed
     */
    public function getLayout()
    {
        return Mage::getStoreConfig(self::XML_CONFIG_LABEL_LAYOUT);
    }

    /**
     * @return mixed
     */
    public function isBranded()
    {
        return Mage::getStoreConfigFlag(self::XML_CONFIG_LABEL_BRANDED);
    }

    /**
     * @return mixed
     */
    public function getTopOffset()
    {
        return Mage::getStoreConfig(self::XML_CONFIG_LABEL_TOP_OFFSET);
    }

    /**
     * @return mixed
     */
    public function getLeftOffset()
    {
        return Mage::getStoreConfig(self::XML_CONFIG_LABEL_LEFT_OFFSET);
    }

    /**
     * @return mixed
     */
    public function getGlobalApiKey()
    {
        return Mage::getStoreConfig(
            $this->isDevMode()
                ? self::XML_CONFIG_GLOBAL_DEV_API_KEY
                : self::XML_CONFIG_GLOBAL_API_KEY
        );
    }

    /**
     * @return mixed
     */
    public function getGlobalApiPwd()
    {
        return Mage::getStoreConfig(
            $this->isDevMode()
                ? self::XML_CONFIG_GLOBAL_DEV_API_PWD
                : self::XML_CONFIG_GLOBAL_API_PWD
        );
    }

    /**
     * @return mixed
     */
    public function getGlobalAccountNo()
    {
        return Mage::getStoreConfig(
            $this->isDevMode()
                ? self::XML_CONFIG_GLOBAL_DEV_ACCOUNT_NO
                : self::XML_CONFIG_GLOBAL_ACCOUNT_NO
        );
    }

    /**
     * @return mixed
     */
    public function getGlobalInfo($key = 'account_no')
    {
        return Mage::getStoreConfig(
            $this->isDevMode()
                ? constant("self::XML_CONFIG_GLOBAL_DEV_" . strtoupper($key))
                : constant("self::XML_CONFIG_GLOBAL_" . strtoupper($key))
        );
    }

    /**
     * @return mixed
     */
    public function getDefaultFreeShippingProduct()
    {
        return Mage::getStoreConfig(self::XML_CONFIG_DEFAULT_FREE_SHIPPING_PRODUCT);
    }

    /**
     * @return mixed
     */
    public function areItemsSeparated()
    {
        return Mage::getStoreConfigFlag(self::XML_CONFIG_SEPARATE_ARTICLE_ITEMS);
    }

    /**
     * @return mixed
     */
    public function chargeToMaster()
    {
        return Mage::getStoreConfigFlag(self::XML_CONFIG_CHARGE_TO_MASTER);
    }

    /**
     * @return mixed
     */
    public function getDefaultEbayProduct()
    {
        $result = array();
        if ($auspostEbayMapping = Mage::getStoreConfig(self::XML_CONFIG_DEFAULT_EBAY_PRODUCT)) {
            $mappings = unserialize($auspostEbayMapping);
        }
        // check if mappings exist
        if (isset($mappings)) {
            foreach ($mappings as $i => $mapping) {
                foreach ($mapping as $key => $val) {
                    $result[$key] = $val;
                }
            }
        }

        return $result;
    }

    /**
     * @param FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry
     * @return Mage_Sales_Model_Order_Shipment
     */
    public function getShipmentFromQueueEntry(FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry)
    {
        $incrementId = $queueEntry->getShipmentId();
        /** @var Mage_Sales_Model_Order_Shipment $shipment */
        $shipment = Mage::getModel('sales/order_shipment');
        // backward compatible e.g. support both 293 + 100000293 (I though load did this already)
        if (preg_match("/^[0-9]{9}$/", $incrementId)) {
            $shipment = $shipment->loadByIncrementId($incrementId);
        } elseif ($incrementId != 0 && is_numeric($incrementId)) {
            $shipment = $shipment->load($incrementId);
        } else {
            $err = sprintf("Cannot retrieve shipment!");
            Mage::helper('shippedfrom')->log($err, Zend_Log::ERR);
            Mage::throwException($err);
        }

        return $shipment;
    }

    /**
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @return bool|string
     */
    public function getProductIdFromShippingMethod(Mage_Sales_Model_Order_Shipment $shipment)
    {
        $shippingMethod = array(
            'method' => $shipment->getOrder()->getShippingMethod(true),
            'description' => $shipment->getOrder()->getData('shipping_description')
        );

        /** @var FactoryX_ShippedFrom_Model_Resource_Account_Collection $matchingAccounts */
        $matchingAccounts = Mage::getResourceModel('shippedfrom/account_collection')
            ->addFieldToFilter('location_id', $shipment->getShippedFrom());

        $foundProducts = array();

        if ($matchingAccounts->getSize()) {
            $foundProducts = $this->findProductsWithinAccounts($shippingMethod, $matchingAccounts, $foundProducts);
        } else {
            /** @var FactoryX_StoreLocator_Model_Mysql4_Location_Collection $locationCollection */
            $locationCollection = Mage::getResourceModel('ustorelocator/location_collection')
                ->addFieldToFilter('location_id', $shipment->getShippedFrom())
                ->addFieldToSelect('region')
                ->setPageSize(1)
                ->setCurPage(1);

            if ($locationCollection->getSize()) {
                /** @var FactoryX_StoreLocator_Model_Location $location */
                $location = $locationCollection->getFirstItem();
                $state = $location->getRegion();

                /** @var FactoryX_ShippedFrom_Model_Resource_Account_Collection $matchingAccounts */
                $matchingAccounts = Mage::getResourceModel('shippedfrom/account_collection')
                    ->addFieldToFilter('state', $state);

                if ($matchingAccounts->getSize()) {
                    $foundProducts = $this->findProductsWithinAccounts(
                        $shippingMethod,
                        $matchingAccounts,
                        $foundProducts
                    );
                }
            }
        }

        /*
         * Case 1: More than 1 matching product, WRONG
         * Case 2: No product but the shipping method is Auspost, WRONG
         * Case 3: No product but the shipping method is not Auspost, OK
         * Case 4: One product found, OK
         */
        if (count($foundProducts) > 1) {
            $retVal = $foundProducts;
        } elseif (empty($foundProducts) && $this->isAuspostShippingMethod($shippingMethod)) {
            $retVal = 0;
        } elseif (empty($foundProducts)) {
            $retVal = false;
        } else {
            $retVal = $foundProducts[0];
        }

        return $retVal;
    }

    /**
     * https://www.atwix.com/magento/supee-7405-cart-merge-error-fix/
     * Borrowed from wordpress/wp-includes/functions.php
     *
     * @param $data
     * @return bool
     */
    protected static function isSerialized($data)
    {
        // if it isn't a string, it isn't serialized
        if (!is_string($data))
            return false;
        $data = trim($data);
        if ('N;' == $data)
            return true;
        $length = strlen($data);
        if ($length < 4)
            return false;
        if (':' !== $data[1])
            return false;
        $lastc = $data[$length-1];
        if (';' !== $lastc && '}' !== $lastc)
            return false;
        $token = $data[0];
        switch ($token) {
            case 's' :
                if ('"' !== $data[$length-2])
                    return false;
            case 'a' :
            case 'O' :
                return (bool) preg_match("/^{$token}:[0-9]+:/s", $data);
            case 'b' :
            case 'i' :
            case 'd' :
                return (bool) preg_match("/^{$token}:[0-9.E-]+;\$/", $data);
        }

        return false;
    }

    /**
     * @param $shippingMethod
     * @return bool
     */
    protected function isAuspostShippingMethod($shippingMethod)
    {
        return (
            (array_key_exists('australiapost', $shippingMethod) && $shippingMethod['australiapost'])
            ||
            $shippingMethod['method'] == "freeshipping"
            ||
            $shippingMethod['method'] == "m2eproshipping"
        );
    }

    /**
     * @param $shippingMethod
     * @param $matchingAccounts
     * @param $foundProducts
     * @return array
     */
    protected function findProductsWithinAccounts($shippingMethod, $matchingAccounts, $foundProducts)
    {
        $method = $shippingMethod['method']['method'];
        if ($method == "freeshipping") {
            return $this->findFreeShippingProduct($matchingAccounts, $foundProducts);
        } elseif ($method == "m2eproshipping") {
            return $this->findEbayShippingProduct($matchingAccounts, $foundProducts);
        } else {
            return $this->findAuspostShippingProduct($matchingAccounts, $foundProducts, $method);
        }
    }


    /**
     * valid phone numbers
     * 0398994575
     * +0398994575
     * +6198994575
     * +61398994575
     * 0400311191
     * +610400311191
     * +61403484986
     *
     * invalid phone numbers
     * 54487494
     * 98994575
     * 398994575
     * 400311191
     * @param $phoneNumber
     * @param $state
     * @return mixed|string
     * @throws Exception
     */
    public function validatePhoneNumber($phoneNumber, $state)
    {
        $orgPhoneNumber = $phoneNumber;
        
        // remove non-numeric chars
        $phoneNumber = preg_replace("/[^0-9 ]/", '', $phoneNumber);
        
        // prefixed with country code
        if (preg_match("/^61/", $phoneNumber)
            && in_array(strlen($phoneNumber), array(10,11,12))) {
            // prefix with plus??? just leave it at this stage
        } elseif (preg_match("/^04/", $phoneNumber) && strlen($phoneNumber) == 10) {
            // mobile
        } elseif (preg_match("/^4/", $phoneNumber) && strlen($phoneNumber) == 9) {
            // mobile not prefixed with zero
            $phoneNumber = "0" . $phoneNumber;
        } else if (!preg_match("/^0/", $phoneNumber) && strlen($phoneNumber) == 8) {
            // home number not prefixed (requires an area code OR country code)
            if (empty($state)) {
                Mage::throwException(
                    Mage::helper('shippedfrom')->__("please provide a state for number '%s'", $orgPhoneNumber)
                );
            } else {
                if ($areaCode = $this->getAreaCodeFromState($state)) {
                    $phoneNumber = sprintf("+61%s%s", $areaCode, $phoneNumber);
                } else {
                    Mage::throwException(
                        Mage::helper('shippedfrom')->__("no area code defined for region '%s'", $state)
                    );
                }
            }
        } else {
            Mage::throwException(Mage::helper('shippedfrom')->__("cannot validate phone number '%s'", $orgPhoneNumber));
        }

        return $phoneNumber;
    }

    /**
     * @param $matchingAccounts
     * @param $foundProducts
     * @return array
     */
    protected function findFreeShippingProduct($matchingAccounts, $foundProducts)
    {
        $defaultFreeShipping = $this->getDefaultFreeShippingProduct();
        if ($defaultFreeShipping) {
            foreach ($matchingAccounts as $matchingAccount) {
                /** @var FactoryX_ShippedFrom_Model_Resource_Account_Product_Collection $matchingProducts */
                $matchingProducts = Mage::getResourceModel('shippedfrom/account_product_collection')
                    ->addFieldToFilter('group', array('like' => '%' . $defaultFreeShipping . '%'))
                    ->addFieldToFilter('associated_account', $matchingAccount->getAccountId());

                if (!$matchingProducts->getSize()) {
                    continue;
                } else {
                    $foundProducts = array_merge($foundProducts, $matchingProducts->getColumnValues('product_id'));
                }
            }

            return $foundProducts;
        }

        return $foundProducts;
    }

    /**
     * @param $matchingAccounts
     * @param $foundProducts
     * @return array
     */
    protected function findEbayShippingProduct($matchingAccounts, $foundProducts)
    {
        $defaultEbayProduct = $this->getDefaultEbayProduct();
        if ($defaultEbayProduct) {
            foreach ($matchingAccounts as $matchingAccount) {
                foreach ($defaultEbayProduct as $ebayMethod => $ausPostGroup) {
                    /** @var FactoryX_ShippedFrom_Model_Resource_Account_Product_Collection $matchingProducts */
                    $matchingProducts = Mage::getResourceModel('shippedfrom/account_product_collection')
                        ->addFieldToFilter('group', array('like' => '%' . $ausPostGroup . '%'))
                        ->addFieldToFilter('associated_account', $matchingAccount->getAccountId());
                    if (!$matchingProducts->getSize()) {
                        continue;
                    } else {
                        $foundProducts = array_merge(
                            $foundProducts,
                            $matchingProducts->getColumnValues('product_id')
                        );
                    }
                }
            }

            return $foundProducts;
        }

        return $foundProducts;
    }

    /**
     * @param $matchingAccounts
     * @param $foundProducts
     * @param $method
     * @return array
     */
    protected function findAuspostShippingProduct($matchingAccounts, $foundProducts, $method)
    {
        foreach ($matchingAccounts as $matchingAccount) {
            /** @var FactoryX_ShippedFrom_Model_Resource_Account_Product_Collection $matchingProducts */
            $matchingProducts = Mage::getResourceModel('shippedfrom/account_product_collection')
                ->addFieldToFilter('associated_shipping_method', $method)
                ->addFieldToFilter('associated_account', $matchingAccount->getAccountId());

            if (!$matchingProducts->getSize()) {
                if (preg_match('/express/i', $method)) {
                    $group = "Express Post";
                } elseif (preg_match('/parcel/i', $method)) {
                    $group = "Parcel Post";
                } else {
                    continue;
                }

                /** @var FactoryX_ShippedFrom_Model_Resource_Account_Product_Collection $matchingProducts */
                $matchingProducts = Mage::getResourceModel('shippedfrom/account_product_collection')
                    ->addFieldToFilter('group', $group)
                    ->addFieldToFilter('associated_account', $matchingAccount->getAccountId());

                if (!$matchingProducts->getSize()) {
                    continue;
                } else {
                    $foundProducts = array_merge($foundProducts, $matchingProducts->getColumnValues('product_id'));
                }
            } else {
                $foundProducts = array_merge($foundProducts, $matchingProducts->getColumnValues('product_id'));
            }
        }

        return $foundProducts;
    }

    /**
     * @param $state
     * @return string
     */
    protected function getAreaCodeFromState($state)
    {
        switch (strtolower($state)) {
            case 'nsw':
            case 'act':
                $areaCode = '2';
                break;
            case 'vic':
            case 'tas':
                $areaCode = '3';
                break;
            case 'qld':
                $areaCode = '7';
                break;
            case 'wa':
            case 'sa':
            case 'nt':
                $areaCode = '8';
                break;
            default:
                return false;
        }

        return $areaCode;
    }
}