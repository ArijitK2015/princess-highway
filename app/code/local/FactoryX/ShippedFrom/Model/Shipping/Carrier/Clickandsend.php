<?php
/**
*/
class FactoryX_ShippedFrom_Model_Shipping_Carrier_Clickandsend {

    const ENCLOSURE = '"';
    const DELIMITER = ',';

    protected $items = array();

    protected static $stateCodes = array(
        'VIC'   => "Victoria",
        'NSW'   => "New South Wales",
        'WA'    => "Western Australia",
        'QLD'   => "Queensland",
        'SA'    => "South Australia",
        'TAS'   => "Tasmanis",
        'NT'    => "Northern Territory",
        'ACT'   => "Australian Capital Territory"
    );

    /**
     * @param $header
     */
    public function addHeaderItem($header) {
        $this->items[] = $header;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     */
    public function addItem(Mage_Sales_Model_Order $order)
    {
        /** @var FactoryX_ShippedFrom_Helper_Clickandsend $helper */
        $helper = Mage::helper('shippedfrom/clickandsend');
        /** @var Mage_Sales_Model_Order_Address $shippingAddress */
        $shippingAddress = $order->getShippingAddress();

        $dimensions = $this->getDimensions($order); // $this->getArticleType($order));
        $address = $helper->getAddress($shippingAddress);

        $item = array(
            // TODO: Add feature to export address books to and from Click & Send.
            //'addressCode'           => '',
            // A - Delivery Company Name
            'deliveryCompanyName'   => $helper->getReceiverCompanyName($shippingAddress),
            // B - Delivery Name
            'deliveryName'          => $helper->getReceiverName($shippingAddress),
            // C - Delivery Telephone
            'deliveryTelephone'     => $shippingAddress->getTelephone(),
            // D - Delivery Email
            'deliveryEmail'         => $shippingAddress->getEmail(),
            // E - Delivery Address (Line 1)
            'deliveryAddressLine1'  => ((count($address) > 0) ? $address[0] : ""),
            //'deliveryAddressLine1'  => $shippingAddress->getStreet1(),            
            // F - Delivery Address (Line 2)
            'deliveryAddressLine2'  => ((count($address) > 1) ? $address[1] : ""),
            //'deliveryAddressLine2'  => $shippingAddress->getStreet2(),
            // G - Delivery Address (Line 3)
            'deliveryAddressLine3'  => ((count($address) > 2) ? $address[2] : ""),
            //'deliveryAddressLine3'  => $shippingAddress->getStreet3(),
            // H - Delivery City/ Suburb
            'deliveryCity'          => $shippingAddress->getCity(),
            // I - Delivery State
            //'deliveryState'         => $shippingAddress->getRegionCode(),
            'deliveryState'         => self::getDeliveryState($shippingAddress->getRegionCode()),
            // J - Delivery Postcode
            'deliveryPostcode'      => $shippingAddress->getPostcode(),
            // K - Delivery Country Code
            'deliveryCountryCode'   => $shippingAddress->getCountry(),
            // L - Service Code
            'serviceCode'           => $this->getServiceCode($order),
            // M - Article Type
            'articleType'           => $this->getArticleType($order),
            /*
            'length'                => $helper->getAttribute($order, 'length'),
            'width'                 => $helper->getAttribute($order, 'width'),
            'height'                => $helper->getAttribute($order, 'height'),
            */
            // N - Length
            'length'                => $dimensions['length'],
            // O - Width
            'width'                 => $dimensions['width'],
            // P - Height
            'height'                => $dimensions['height'],
            // Q - Declared Weight
            'declaredWeight'        => $dimensions['weight'],
            // R - Extra Cover
            // Extra Cover doesn't work with Click & Send
            'extraCover'            => '',
            // S - Declared Value
            'insuranceValue'        => '',
            // T - Description of Goods
            // Mandatory only if extra cover = 1
            'descriptionOfGoods'    => '',

            /*
            TODO - work out what these fields are for ...
            */
            // ???
            ///'categoryOfItems'       => Mage::getStoreConfig('shippedfrom/clickandsend/category_of_items'),
            // This number is sometimes needed when you're exporting a package to a
            // foreign country.
            // TODO: Figure out a way to add this as an option for the merchant.
            // ???
            //'exportDeclarationNumber'       => '',
            // ???
            //'categoryOfItemsExplanation'    => Mage::getStoreConfig('shippedfrom/clickandsend/category_of_items_explanation'),
            // ???
            //'articleLodgerName'             => Mage::getStoreConfig('shippedfrom/clickandsend/from_name'),
            // ???
            //'nonDeliveryInstructions'       => Mage::getStoreConfig('shippedfrom/clickandsend/nondelivery_instructions'),
            // ???
            //'returnAddress'                 => Mage::getStoreConfig('shippedfrom/clickandsend/return_address'),

            // U - From Name
            'fromName'                      => Mage::getStoreConfig('shippedfrom/clickandsend/from_name'),
            // V - From Company Name
            'fromCompanyName'               => Mage::getStoreConfig('shippedfrom/clickandsend/from_company_name'),
            // W - From Phone
            'fromPhone'                     => Mage::getStoreConfig('shippedfrom/clickandsend/from_phone'),
            // X - From Fax
            'fromFax'                       => Mage::getStoreConfig('shippedfrom/clickandsend/from_fax'),
            // Y - From Email
            'fromEmail'                     => Mage::getStoreConfig('shippedfrom/clickandsend/from_email'),
            // Z - From ABN
            'fromAbn'                       => Mage::getStoreConfig('shippedfrom/clickandsend/from_abn'),
            // AA - From Address (line 1)
            'fromAddressLine1'              => Mage::getStoreConfig('shippedfrom/clickandsend/from_address_line_1'),
            // AB - From Address (line 2)
            'fromAddressLine2'              => Mage::getStoreConfig('shippedfrom/clickandsend/from_address_line_2'),
            // AC - From Address (line 3)
            'fromAddressLine3'              => Mage::getStoreConfig('shippedfrom/clickandsend/from_address_line_3'),
            // AD - From City/ Suburb
            'fromCity'                      => Mage::getStoreConfig('shippedfrom/clickandsend/from_city'),
            // AE - From State
            'fromState'                     => Mage::getStoreConfig('shippedfrom/clickandsend/from_state'),
            // AF - From Postcode
            'fromPostcode'                  => Mage::getStoreConfig('shippedfrom/clickandsend/from_postcode'),
            // AG - From Country Code
            'fromCountry'                   => Mage::getStoreConfig('shippedfrom/clickandsend/from_country'),
            // A value that can used for reconciliations with shipments, e.g. order number,
            // invoice number, recipient name, etc.
            // TODO: Allow the merchant to choose a reference system, e.g. invoice number
            // AH - Your Reference
            'yourReference'                 => $order->getIncrementId(),
            // AI - Delivery instructions
            'deliveryInstructions'          => '',
            // AJ - Additional services
            'additionalServices'            => '',
            // AK - Box or Irregular shaped item
            'boxOrIrregularShapedItem'      => '',
            // AL - CDP Location code
            'sendersCustomReference'        => '',
            // AM  - Embargoed date
            'embargoedDate'                 => '',
            // AN - Specify delivery day
            'specificDeliveryDay'           => '',
            // AO - Specify delivery date
            'specificDeliveryDate'          => '',
            // AP - Delivery timslots
            'deliveryTimslots'              => '',
            // AQ - Receiver customer number
            'receiverCustomerNumber'        => '',
            // AR - Sender track advice
            'senderTrackAdvice'             => $helper->getSenderTrackAdvice(),
            // AS - Receiver track advice
            'receiverTrackAdvice'           => $helper->getReceiverTrackAdvice(),
            // AT - Expected delivery date
            'expectedDeliveryDate'          => '',
            // AU - ATL Options
            'atlOptions'                    => ''
        );

        // The Click & Send CSV specification only allows for four items to be
        // listed. It shouldn't be a problem as the shipping price is calculated
        // by other things, e.g. declared weight, but it's still rather
        // unfortunate.
        // TODO: Add feature to export items to and from Click & Send.

        /*
        $itemLimit = 4;

        // Initialise the four items
        for ($i = 0; $i < $itemLimit; $i++) {
            $item['itemCode' . $i] = '';
            $item['itemDescription' . $i] = '';
            $item['itemHsTariffNumber' . $i] = '';
            $item['itemCountryOfOrigin' . $i] = '';
            $item['itemQuantity' . $i] = '';
            $item['itemUnitPrice' . $i] = '';
            $item['itemUnitWeight' . $i] = '';
        }

        $allSimpleItems = $helper->getAllSimpleItems($order);
        for ($i = 0; $i < $itemLimit; $i++) {
            if (isset($allSimpleItems[$i])) {
                $simpleItem = $allSimpleItems[$i];
                $item['itemCode' . $i] = $simpleItem->getId();
                $item['itemDescription' . $i] = self::cleanString($simpleItem->getName());
                $item['itemHsTariffNumber' . $i] = '';
                $item['itemCountryOfOrigin' . $i] = $simpleItem->getData('country_of_manufacture');
                $item['itemQuantity' . $i] = (int)$simpleItem->getQtyOrdered();
                $item['itemUnitPrice' . $i] = sprintf('%0.2f', $simpleItem->getPrice());
                $item['itemUnitWeight' . $i] = sprintf('%0.3f', $simpleItem->getWeight());
            }
        }
        */

        $this->items[] = $item;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
    private function getShippingConfiguration(Mage_Sales_Model_Order $order)
    {
        return explode('_', $order->getShippingMethod());
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return int|null
     */
    private function getServiceCode(Mage_Sales_Model_Order $order)
    {
        /*
        $serviceCode = array(
            'INTL' => array(
                'ECI' => 1,
                'EPI' => 2,
                'RPI' => 3,
                'AIR' => 4
            ),
            'AUS' => array(
                'REGULAR'   => 6,
                'EXPRESSS'  => 8
            )
        );
        $shippingMethod = $this->getShippingConfiguration($order);
        $destCountry = $shippingMethod[1];
        $service = $shippingMethod[3];
        if (isset($serviceCode[$destCountry][$service])) {
            return $serviceCode[$destCountry][$service];
        }
        else if (Mage::helper('shippedfrom/clickandsend')->isExportAll()) {
            return null;
        }
        throw new FactoryX_ShippedFrom_Model_Shipping_Carrier_Clickandsend_Export_Exception(
            "Order #" . $order->getIncrementId() . " can't be imported into Click & Send!"
        );
        */
        
        $serviceCode = Mage::helper('shippedfrom/clickandsend')->getServiceCode();
        // auto: calulate weight =IF(weight <= 0.5, "PPS", "PP")
        if (preg_match("/auto/i", $serviceCode)) {
            $serviceCode = (($order->getWeight() <= 0.5) ? "PPS" : "PP");
        }
        return $serviceCode;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return int
     */
    private function getArticleType(Mage_Sales_Model_Order $order)
    {
        /*
        if ($order->getShippingAddress()->getCountry() == 'AU') {
            return 7;
        }
        else {
            $shippingMethod = $this->getShippingConfiguration($order);
            if (isset($shippingMethod[4]) && $shippingMethod[4] == 'D') {
                return 1;
            }
            return 2;
        }
        */
        $articleType = Mage::helper('shippedfrom/clickandsend')->getArticleType();
        return $articleType;        
    }

    /**
     * @param $order
     * @return array
     */
    private function getDimensions($order) {
        
        $articleType = $this->getArticleType($order);
        
        $dimensions = array(
            'length'    => '',
            'width'     => '',
            'height'    => '',
            'weight'    => ''
        );
        
        // 19: Small Flat Rate Satchel (up to 500g)
        if ($articleType == 7 || $articleType == 19) {
            $dimensions = array(
                'length'    => 35.5,
                'width'     => 22,
                'height'    => 10,
                'weight'    => 0.5
            );
            if ($articleType == 7) {
                if ($order->getWeight() <= 0) {
                    $dimensions['weight'] = 0.001;
                }
                elseif ($order->getWeight() > 22) {
                    $dimensions['weight'] = 22;
                }
                else {
                    $dimensions['weight'] = sprintf('%0.3f', $order->getWeight());
                }
            }
        }
        // 20: Medium Flat Rate Satchel (up to 3kg)
        else if ($articleType == 20) {
            $dimensions = array(
                'length'    => 40.5,
                'width'     => 31,
                'height'    => 10,
                'weight'    => 3
            );
        }
        // 21: Large Flat Rate Satchel (up to 5kg)
        else if ($articleType == 21) {
            $dimensions = array(
                'length'    => 51,
                'width'     => 43.5,
                'height'    => 10,
                'weight'    => 5
            );
        }
        return $dimensions;
    }

    /**
     * @param string $filePath
     * @return bool
     */
    public function makeCsv($filePath)
    {
        $handle = fopen($filePath, 'w');
        if ($handle == false) {
            return false;
        }
        foreach ($this->items as $item) {
            fputcsv($handle, array_values($item), self::DELIMITER, self::ENCLOSURE);
        }
        fclose($handle);
        return true;
    }

    /**
     * Event observer. Triggered before an adminhtml widget template is
     * rendered. We use this to add our action to bulk actions in the sales
     * order grid instead of overriding the class.
     *
     * @param $observer
     */
    public function addExportToBulkAction($observer)
    {
        $url = Mage::helper("adminhtml")->getUrl('adminhtml/sales_order_clickandsend/export');        
        //Mage::helper('shippedfrom')->log(sprintf("%s->observer->block=%s", __METHOD__, get_class($observer->getBlock())) );
        $block = $observer->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_Grid && Mage::helper('shippedfrom/clickandsend')->isClickAndSendEnabled()) {
            $block->getMassactionBlock()->addItem('clickandsendexport', array(
                'label' => $block->__('Export to CSV (Click & Send)'),
                'url'   => $url
            ));
        }
    }

    /**
     * @param $stateName
     * @return int|string
     */
    private static function getDeliveryState($stateName) {
        $stateCode = $stateName;
        foreach(self::$stateCodes as $code => $value) {
            if (preg_match(sprintf("/%s/i", $value), $stateName)) {
                $stateCode = $code;
            }
        }
        return $stateCode;

    }

}
