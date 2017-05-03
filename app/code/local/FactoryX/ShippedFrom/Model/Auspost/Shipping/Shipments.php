<?php

/**
 * Class FactoryX_ShippedFrom_Model_Auspost_Shipping_Shipments
 */
class FactoryX_ShippedFrom_Model_Auspost_Shipping_Shipments
    extends FactoryX_ShippedFrom_Model_Auspost_Shipping_Abstract
{
    const MAX_ITEM_DESCRIPTION_LEN = 50;
    const MAX_ADDRESS_LINE_LEN = 40;
    const MAX_ADDRESS_LINES = 3;

    /**
     * @param FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry
     * @return bool
     */
    public function getShipment(FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry)
    {
        $accountNo = $this->getAccountNumberFromShipment($queueEntry->getShippedFrom());
        $apiKey = $this->getApiKeyFromShipment($queueEntry->getShippedFrom());
        $apiPassword = $this->getApiPasswordFromShipment($queueEntry->getShippedFrom());

        /** @var Auspost\Shipping\ShippingClient $shipTrack */
        $shipTrack = $this->initShipTrack($apiKey, $apiPassword, $accountNo);

        try {
            $result = $shipTrack->GetShipments(array('shipment_ids' => $queueEntry->getApShipmentId()));
        }
        catch(Guzzle\Http\Exception\BadResponseException $e) {
            $message = $e->getMessage();
            $result = "";
        }

        return $result;
    }

    /**
     * @param FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry
     * @param $productId
     * @param null $cronLogger
     * @return FactoryX_ShippedFrom_Model_Auspost_Shipping_Shipments
     */
    public function updateShipment(
        FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry,
        $productId,
        $cronLogger = null
    ) {
        $this->hlp()->log(sprintf("%s->queueEntry: %s", __METHOD__, get_class($queueEntry)));

        /** @var Mage_Sales_Model_Order_Shipment $shipment */
        $shipment = $this->hlpAuspost()->getShipmentFromQueueEntry($queueEntry);
        $this->hlp()->log($this->hlp()->__("%s->shipment: %s", __METHOD__, get_class($shipment)));
        $this->hlp()->log($this->hlp()->__("%s->shipment: %s", __METHOD__, print_r($shipment->getData(), true)));

        $accountNo = $this->getAccountNumberFromShipment($queueEntry->getShippedFrom());
        $this->hlp()->log($this->hlp()->__("%s->accountNo: %s", __METHOD__, $accountNo));
        $apiKey = $this->getApiKeyFromShipment($queueEntry->getShippedFrom());
        $this->hlp()->log($this->hlp()->__("%s->apiKey: %s", __METHOD__, $apiKey));
        $apiPassword = $this->getApiPasswordFromShipment($queueEntry->getShippedFrom());
        $this->hlp()->log($this->hlp()->__("%s->apiPassword: %s", __METHOD__, $apiPassword));

        /** @var Auspost\Shipping\ShippingClient $shipTrack */
        $shipTrack = $this->initShipTrack($apiKey, $apiPassword, $accountNo);
        $this->hlp()->log($this->hlp()->__("%s->shipTrack: %s", __METHOD__, get_class($shipTrack)));

        $shipmentData = $this->generateAuspostShipmentData($shipment, $productId, $queueEntry->getScheduleId());

        $this->hlp()->log($this->hlp()->__("%s->shipmentData: %s", __METHOD__, print_r($shipmentData, true)));

        /** @var FactoryX_ShippedFrom_Model_Auspost_Shipping_Address $addressValidator */
        $addressValidator = Mage::getModel('shippedfrom/auspost_shipping_address');

        // validate shipment data
        $addressValidator->validateShipmentAddressData($queueEntry, $shipmentData);
        /**
         * @todo refactor this method to validate shipment item
         */
        //$shipmentData = $this->validateShipmentItemData($aShipment, $shipmentData, $si);

        $json = array();
        $json['shipment_id'] = $queueEntry->getApShipmentId();
        $json['body'] = Mage::helper('core')->jsonEncode($shipmentData);

        $this->saveJsonRequest($queueEntry, $shipmentData);

        $shipmentUpdateSummary = array();

        $cronMessage = $this->hlp()->__(
            'Start UpdateShipment for shipment %s with product id %s',
            $shipment->getIncrementId(),
            $productId
        );
        $this->addCronLoggerMessage($cronLogger, $cronMessage);

        try {
            $this->hlp()->log($this->hlp()->__("%s->UpdateShipment: %s", __METHOD__, print_r($json, true)));
            $shipmentUpdateSummary = $shipTrack->UpdateShipment($json);
            $this->hlp()->log(
                $this->hlp()->__(
                    "%s->shipmentUpdateSummary: %s",
                    __METHOD__,
                    print_r($shipmentUpdateSummary, true)
                )
            );
            $cronMessage = $this->hlp()->__('End UpdateShipment for shipment %s', $shipment->getIncrementId());
            $this->addCronLoggerMessage($cronLogger, $cronMessage);
        }
        catch(Guzzle\Http\Exception\BadResponseException $e) {
            $message = $e->getMessage();
            $cronMessage = $message;
            $this->addCronLoggerMessage($cronLogger, $cronMessage);
        }

        if (!$shipmentUpdateSummary && isset($message)) {
            $this->handleErrors(
                $queueEntry,
                array(
                    'errors'    =>  array(
                        'message' =>  $message
                    )
                )
            );
        } else if (array_key_exists('errors', $shipmentUpdateSummary)) {
            $this->handleErrors($queueEntry, $shipmentUpdateSummary);
        } else {
            $queueEntry
                ->setData('ap_last_message', '')
                ->setData('ap_last_url', '')
                ->save();
        }

        return $this;
    }

    /**
     * @param FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry
     * @param null $cronLogger
     * @return bool
     */
    public function createAuspostShipment(FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry, $cronLogger = null)
    {
        $this->hlp()->log(sprintf("%s->queueEntry: %s", __METHOD__, get_class($queueEntry)));

        /** @var Mage_Sales_Model_Order_Shipment $shipment */
        $shipment = $this->hlpAuspost()->getShipmentFromQueueEntry($queueEntry);
        $this->hlp()->log($this->hlp()->__("%s->shipment: %s", __METHOD__, get_class($shipment)));
        $this->hlp()->log($this->hlp()->__("%s->shipment: %s", __METHOD__, print_r($shipment->getData(), true)));

        $accountNo = $this->getAccountNumberFromShipment($queueEntry->getShippedFrom());
        $this->hlp()->log($this->hlp()->__("%s->accountNo: %s", __METHOD__, $accountNo));
        $apiKey = $this->getApiKeyFromShipment($queueEntry->getShippedFrom());
        $this->hlp()->log($this->hlp()->__("%s->apiKey: %s", __METHOD__, $apiKey));
        $apiPassword = $this->getApiPasswordFromShipment($queueEntry->getShippedFrom());
        $this->hlp()->log($this->hlp()->__("%s->apiPassword: %s", __METHOD__, $apiPassword));

        /** @var Auspost\Shipping\ShippingClient $shipTrack */
        $shipTrack = $this->initShipTrack($apiKey, $apiPassword, $accountNo);
        $this->hlp()->log($this->hlp()->__("%s->shipTrack: %s", __METHOD__, get_class($shipTrack)));

        $shipmentData = $this->generateAuspostShipmentData($shipment);

        $this->hlp()->log($this->hlp()->__("%s->shipmentData: %s", __METHOD__, print_r($shipmentData, true)));

        /** @var FactoryX_ShippedFrom_Model_Auspost_Shipping_Address $addressValidator */
        $addressValidator = Mage::getModel('shippedfrom/auspost_shipping_address');

        // validate shipment data
        foreach ($shipmentData['shipments'] as $si => $aShipment) {
            $addressValidator->validateShipmentAddressData($queueEntry, $aShipment);
            $shipmentData = $this->validateShipmentItemData($aShipment, $shipmentData, $si);
        }

        $weight = $shipmentData['shipments'][0]['items'][0]['weight'];

        $json = array();
        $json['body'] = Mage::helper('core')->jsonEncode($shipmentData);

        $this->saveJsonRequest($queueEntry, $shipmentData);

        $shipmentCreationSummary = array();

        $cronMessage = $this->hlp()->__('Start CreateShipments for shipment %s', $shipment->getIncrementId());
        $this->addCronLoggerMessage($cronLogger, $cronMessage);

        try {
            $this->hlp()->log($this->hlp()->__("%s->CreateShipments: %s", __METHOD__, print_r($json, true)));
            $shipmentCreationSummary = $shipTrack->CreateShipments($json);
            $this->hlp()->log(
                $this->hlp()->__(
                    "%s->shipmentCreationSummary: %s",
                    __METHOD__,
                    print_r($shipmentCreationSummary, true)
                )
            );
            $cronMessage = $this->hlp()->__('End CreateShipments for shipment %s', $shipment->getIncrementId());
            $this->addCronLoggerMessage($cronLogger, $cronMessage);
        }
        catch(Guzzle\Http\Exception\BadResponseException $e) {
            $message = $e->getMessage();
            $cronMessage = $message;
            $this->addCronLoggerMessage($cronLogger, $cronMessage);
        }

        $apArticleId = false;
        if (!$shipmentCreationSummary && isset($message)) {
            $this->handleErrors(
                $queueEntry,
                array(
                    'errors'    =>  array(
                        'message' =>  $message
                    )
                )
            );
        } else if (array_key_exists('errors', $shipmentCreationSummary)) {
            $this->handleErrors($queueEntry, $shipmentCreationSummary);
        } else {
            $cronMessage = $this->hlp()->__('Start add tracking to shipment %s', $shipment->getIncrementId());
            $this->addCronLoggerMessage($cronLogger, $cronMessage);
            $this->addTrackingNumberToShipment($shipment, $shipmentCreationSummary);
            $cronMessage = $this->hlp()->__('End add tracking to shipment %s', $shipment->getIncrementId());
            $this->addCronLoggerMessage($cronLogger, $cronMessage);

            $shipmentEntry = $shipmentCreationSummary['shipments'][0];
            $apShipmentId = $shipmentEntry['shipment_id'];
            $apConsignmentId = $shipmentEntry['items'][0]['tracking_details']['consignment_id'];
            $apArticleId = $shipmentEntry['items'][0]['tracking_details']['article_id'];
            $apProductId = $shipmentEntry['items'][0]['product_id'];
            $this->createItemEntry($queueEntry, $shipmentCreationSummary, $weight);

            $queueEntry->setData('status', FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_SHIPPED)
                ->setData('ap_shipment_id', $apShipmentId)
                ->setData('ap_consignment_id', $apConsignmentId)
                ->setData('ap_article_id', $apArticleId)
                ->setData('ap_product_id', $apProductId)
                ->setData('ap_last_message', '')
                ->setData('ap_last_url', '')
                ->save();
        }

        return $apArticleId;
    }

    /**
     * @param FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry
     */
    public function deleteShipment(FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry)
    {
        if ($queueEntry->getApShipmentId()) {
            $accountNo = $this->getAccountNumberFromShipment($queueEntry->getShippedFrom());
            $apiKey = $this->getApiKeyFromShipment($queueEntry->getShippedFrom());
            $apiPassword = $this->getApiPasswordFromShipment($queueEntry->getShippedFrom());

            /** @var Auspost\Shipping\ShippingClient $shipTrack */
            $shipTrack = $this->initShipTrack($apiKey, $apiPassword, $accountNo);

            $shipmentDeletionSummary = array();

            try {
                $shipmentDeletionSummary = $shipTrack->DeleteShipment(
                    array('shipment_id'   =>  $queueEntry->getApShipmentId())
                );
            } catch(Guzzle\Http\Exception\BadResponseException $e) {
                $message = $e->getMessage();
            }
        } else {
            $shipmentDeletionSummary = false;
        }

        if (!$shipmentDeletionSummary && isset($message)) {
            $this->handleErrors(
                $queueEntry,
                array(
                    'errors'    =>  array(
                        'message' =>  $message
                    )
                )
            );
        } else if (array_key_exists('errors', $shipmentDeletionSummary)) {
            $this->handleErrors($queueEntry, $shipmentDeletionSummary);
        } else {
            $queueEntry->delete();
        }
    }

    /**
     * @param array $apShipmentIds
     * @param $shippedFrom
     * @param $collection
     */
    public function massDeleteShipment(array $apShipmentIds, $shippedFrom, $collection)
    {
        $accountNo = $this->getAccountNumberFromShipment($shippedFrom);
        $apiKey = $this->getApiKeyFromShipment($shippedFrom);
        $apiPassword = $this->getApiPasswordFromShipment($shippedFrom);

        /** @var Auspost\Shipping\ShippingClient $shipTrack */
        $shipTrack = $this->initShipTrack($apiKey, $apiPassword, $accountNo);

        $shipmentDeletionSummary = array();

        try {
            $shipmentsToDelete = (count($apShipmentIds) > 1) ? implode(',', $apShipmentIds) : $apShipmentIds[0];
            $shipmentDeletionSummary = $shipTrack->DeleteShipments(
                array('shipment_ids'   =>  $shipmentsToDelete)
            );
        } catch(Guzzle\Http\Exception\BadResponseException $e) {
            $message = $e->getMessage();
        }

        if (!$shipmentDeletionSummary && isset($message)) {
            $this->massHandleErrors(
                $collection,
                array(
                    'errors'    =>  array(
                        'message' =>  $message
                    )
                )
            );
        } else if (array_key_exists('errors', $shipmentDeletionSummary)) {
            $this->massHandleErrors($collection, $shipmentDeletionSummary);
        } else {
            $collection->delete();
        }
    }

    /**
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @param null $productId
     * @param bool $scheduleId
     * @return array
     */
    public function generateAuspostShipmentData(
        Mage_Sales_Model_Order_Shipment $shipment,
        $productId = null,
        $scheduleId = false
    ) {
        
        $this->hlp()->log($this->hlp()->__("%s->shipment: %s", __METHOD__, print_r($shipment->getData(), true)));
        $shippedFrom = (int) $shipment->getShippedFrom();
        $this->hlp()->log($this->hlp()->__("%s->shippedFrom: %s", __METHOD__, $shippedFrom));

        $data = array();
        $data['shipments'] = array();
        $shipmentDetails = array();
        $shipmentDetails['shipment_reference'] = $shipment->getIncrementId();
        $shipmentDetails['customer_reference_1'] = $shipment->getIncrementId();
        $shipmentDetails['from'] = $this->generateFromField($shippedFrom);
        $shipmentDetails['to'] = $this->generateToField($shipment->getShippingAddress());
        $shipmentDetails['items'] = $this->generateItemsField($shipment, $productId, $scheduleId);
        $data['shipments'][] = $shipmentDetails;
        if ($scheduleId) {
            return $shipmentDetails;
        } else {
            return $data;
        }
    }

    /**
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @param $shipmentCreationSummary
     */
    protected function addTrackingNumberToShipment(Mage_Sales_Model_Order_Shipment $shipment, $shipmentCreationSummary)
    {
        $trackingId = $shipmentCreationSummary['shipments'][0]['items'][0]['tracking_details']['article_id'];

        /** @var Mage_Sales_Model_Order $order */
        $order = $shipment->getOrder();

        /** @var Varien_Object $shippingMethod */
        $shippingMethod = $order->getShippingMethod(true);

        /** @var Mage_Sales_Model_Order_Shipment_Track $track */
        $track = Mage::getModel('sales/order_shipment_track')
            ->addData(
                array(
                    'number'        => $trackingId,
                    'carrier_code'  => $shippingMethod->getCarrierCode(),
                    'title'         => $shippingMethod->getMethod()
                )
            );
        $shipment->addTrack($track);
        $shipment->save();
        $shipment->sendEmail(true);
    }

    /**
     * @param int $locationId
     * @return array
     */
    protected function generateFromField($locationId)
    {
        if (!is_int($locationId)) {
            Mage::throwException($this->hlp()->__('location id for must be of type int: %s', __METHOD__));
        }

        /** @var FactoryX_StoreLocator_Model_Mysql4_Location_Collection $storeCollection */
        $storeCollection = Mage::getResourceModel('ustorelocator/location_collection')
            ->addFieldToFilter('location_id', $locationId)
            ->setPageSize(1)
            ->setCurPage(1);

        if (!$storeCollection->getSize()) {
            $err = sprintf('No corresponding store found with id = %s', $locationId);
            $this->hlp()->log($err, Zend_Log::ERR);
            Mage::throwException($err);
        }

        /** @var FactoryX_StoreLocator_Model_Location $store */
        $store = $storeCollection->getFirstItem();

        return array(
            'name'      =>  $store->getStoreCode(),
            'lines'     =>  array($store->getAddressDisplay()),
            'suburb'    =>  $store->getSuburb(),
            'state'     =>  strtoupper($store->getRegion()),
            'postcode'  =>  $store->getPostcode()
        );
    }

    /**
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @param $productId
     * @param $scheduleId
     * @return array
     */
    protected function generateItemsField(Mage_Sales_Model_Order_Shipment $shipment, $productId, $scheduleId)
    {
        $itemsData = array();

        if ($productId && $scheduleId) {
            $itemsData = $this->generateItemDataForUpdate($productId, $scheduleId, $itemsData);
        } else {
            /** @var Mage_Sales_Model_Resource_Order_Shipment_Item_Collection $itemCollection */
            $itemCollection = $shipment->getItemsCollection();
            $productId = $this->hlpAuspost()->getProductIdFromShippingMethod($shipment);

            if (is_array($productId)) {
                Mage::throwException(
                    $this->hlp()->__(
                        'More than one products have been found matching this store location and this shipping method'
                    )
                );
            } elseif ($productId === 0) {
                Mage::throwException(
                    $this->hlp()->__(
                        'No product have been found matching this store location and this shipping method'
                    )
                );
            } elseif ($productId) {
                $itemsData = $this->generateItemDataForCreation($productId, $itemCollection, $itemsData);
            } else {
                Mage::throwException($this->hlp()->__('This shipping method is not an Auspost shipping method'));
            }
        }

        return $itemsData;
    }

    /**
     * @param Mage_Sales_Model_Order_Address $shippingAddress
     * @return array
     */
    protected function generateToField(Mage_Sales_Model_Order_Address $shippingAddress)
    {        
        /*
        remove duplicate lines, this happens quite often due to chrome auto-fill
        Chromium regex = "address.*line|address1|addr1|street"
        we need to change the name of subsequent address lines OR just use the a single address line
        */
        $streetFull = preg_split('/\n|\r\n?/', $shippingAddress->getStreetFull());
        $streetFullNew = array();
        foreach ($streetFull as $line) {
            if (!in_array($line, $streetFullNew)) {
                $this->hlp()->log(
                    $this->hlp()->__("%s->add line:%s", __METHOD__, $line)
                );
                $streetFullNew[] = $line;
            }
        }

        $this->hlp()->log(
            $this->hlp()->__("%s->streetFullNew:%s", __METHOD__, print_r($streetFullNew, true))
        );
        
        /*
        Minimum of one address line, up to a maximum of three.
        Each address line is a maximum of 40 characters.
        othwerwise = bad request error 
        */
        $streetFull = array();
        foreach ($streetFullNew as $line) {
            $this->hlp()->log($this->hlp()->__("%s->line: %s", __METHOD__, $line));
            if (strlen($line) > self::MAX_ADDRESS_LINE_LEN) {
                $newLine = wordwrap($line, self::MAX_ADDRESS_LINE_LEN, $break = "\n", $cut = true);
                $this->hlp()->log($this->hlp()->__("%s->split: %s", __METHOD__, $newLine));
                $streetFull = array_merge($streetFull, preg_split('/\n/', $newLine));
            } else {
                $streetFull[] = $line;
            }
        }

        // check the lines
        if (count($streetFull) > self::MAX_ADDRESS_LINES) {
            $warn = sprintf(
                "WARNING: too many address lines %d in '%s' truncating to %d",
                count($streetFull),
                implode("|", $streetFull),
                self::MAX_ADDRESS_LINES
            );
            $streetFull = array_slice(
                $streetFull,
                $offset = 0,
                self::MAX_ADDRESS_LINES,
                $preserveKeys = true
            );
            $this->hlp()->log(Zend_Log::WARN);
            Mage::getSingleton('adminhtml/session')->addWarning($warn);            
        }
        
        $state = strtoupper($this->getStateShortCode($shippingAddress));
        $this->hlp()->log(
            $this->hlp()->__("%s->streetFull:%s", __METHOD__, print_r($streetFull, true))
        );

        try {
            $phone = $this->hlpAuspost()->validatePhoneNumber($shippingAddress->getTelephone(), $state);
        } catch(Exception $ex) {
            $phone = "";
            $warn = sprintf("WARNING: phone '%s' - %s", $shippingAddress->getTelephone(), $ex->getMessage());
            $this->hlp()->log(Zend_Log::WARN);
            Mage::getSingleton('adminhtml/session')->addWarning($warn);
        }

        return array(
            'name'      => $this->getFullNameFromShippingAddress($shippingAddress),
            'lines'     => $streetFull,
            'suburb'    => $shippingAddress->getCity(),
            'state'     => $state,
            'postcode'  => $shippingAddress->getPostcode(),
            'phone'     => $phone
        );
    }

    /**
     * @param $item
     * @return array
     */
    protected function getDimensionsFromItem($item)
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $productCollection */
        $productCollection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToFilter('sku', $item->getSku())
            ->addAttributeToSelect(
                array('length', 'width', 'height', 'weight')
            );

        if ($productCollection->getSize()) {
            $product = $productCollection->getFirstItem();
            $length = ($product->getLength() && $product->getLength() > 0) ? $product->getLength() : 10;
            $width = ($product->getWidth() && $product->getWidth() > 0) ? $product->getWidth() : 10;
            $height = ($product->getHeight() && $product->getHeight() > 0) ? $product->getHeight() : 10;
            $weight = ($product->getWeight() && $product->getWeight() > 0) ? $product->getWeight() : 0.1;
        } else {
            $length = 10;
            $width = 10;
            $height = 10;
            $weight = 0.1;
        }

        return array($length, $width, $height, $weight);
    }

    /**
     * @param Mage_Sales_Model_Order_Address $shippingAddress
     * @return string
     */
    protected function getFullNameFromShippingAddress(Mage_Sales_Model_Order_Address $shippingAddress) //: string
    {
        return implode(
            " ",
            array(
                $shippingAddress->getFirstname(),
                $shippingAddress->getMiddlename(),
                $shippingAddress->getLastname()
            )
        );
    }

    /**
     * @param Mage_Sales_Model_Order_Address $shippingAddress
     * @return mixed
     */
    protected function getStateShortCode(Mage_Sales_Model_Order_Address $shippingAddress)
    {
        $stateCode = Mage::getModel('ustorelocator/australianStates')->getShortCode($shippingAddress->getRegion());
        // try magento (incase they spelt it wrong)
        if (empty($stateCode)) {
            $regionCollection = Mage::getModel('directory/region_api')->items($countryCode = "AU");
            foreach ($regionCollection as $region) {
                if (strcasecmp($shippingAddress->getRegion(), $region['name']) == 0) {
                    $stateCode = $region['code'];
                    break;
                }
            }
        }

        return $stateCode;
    }

    /**
     * @param $aShipment
     * @param $shipmentData
     * @param $si
     * @return mixed
     */
    protected function validateShipmentItemData($aShipment, $shipmentData, $si)
    {
        foreach ($aShipment['items'] as $ii => $aItem) {
            $this->hlp()->log($this->hlp()->__("%s->item: %s", __METHOD__, print_r($aItem, true)));
            if ($aItem['weight']) {
                $shipmentData['shipments'][$si]['items'][$ii]['weight'] = sprintf("%0.2f", $aItem['weight']);
            }

            $this->hlp()->log(
                $this->hlp()->__(
                    "%s->item: %s",
                    __METHOD__,
                    print_r($shipmentData['shipments'][$si]['items'][$ii]['weight'], true)
                )
            );
        }

        return $shipmentData;
    }

    /**
     * @param $itemCollection
     * @param $productId
     * @param $itemsData
     * @return array
     */
    protected function getSeparatedItemsData($itemCollection, $productId, $itemsData)
    {
        foreach ($itemCollection as $item) {
            list($length, $width, $height, $weight) = $this->getDimensionsFromItem($item);
            if ($item->getQty() > 0) {
                $itemsData[] = array(
                    'item_reference' => Mage::helper('core')->uniqHash(),
                    'item_description' => $item->getSku(),
                    'product_id' => $productId,
                    'length' => $length,
                    'width' => $width,
                    'height' => $height,
                    'weight' => $weight,
                    'authority_to_leave' => true,
                );
            }
        }

        return $itemsData;
    }

    /**
     * @param FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry
     * @param $shipmentCreationSummary
     * @param $weight
     */
    protected function createItemEntry(
        FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry,
        $shipmentCreationSummary,
        $weight
    ) {
        $apItemId = $shipmentCreationSummary['shipments'][0]['items'][0]['item_id'];
        $apItemReference = $shipmentCreationSummary['shipments'][0]['items'][0]['item_reference'];

        Mage::getModel('shippedfrom/shipment_item')->setData(
            array(
                'item_id' => $apItemId,
                'item_reference' => $apItemReference,
                'schedule_id' => $queueEntry->getScheduleId(),
                'weight'    =>  $weight
            )
        )->save();
    }

    /**
     * @param $productId
     * @param $scheduleId
     * @param $itemsData
     * @return array
     */
    protected function generateItemDataForUpdate($productId, $scheduleId, $itemsData)
    {
        $itemCollection = Mage::getResourceModel('shippedfrom/shipment_item_collection');
        $itemCollection->addFieldToFilter('schedule_id', $scheduleId);

        foreach ($itemCollection as $item) {
            $itemsData[] = array(
                'item_reference' => $item->getItemReference(),
                'item_id' => $item->getItemId(),
                'product_id' => $productId,
                'weight' => $item->getWeight()
            );
        }

        return $itemsData;
    }

    /**
     * @param $productId
     * @param $itemCollection
     * @param $itemsData
     * @return array
     */
    protected function generateItemDataForCreation($productId, $itemCollection, $itemsData)
    {
        if ($this->hlpAuspost()->areItemsSeparated()) {
            $itemsData = $this->getSeparatedItemsData($itemCollection, $productId, $itemsData);
        } else {
            $finalWeight = 0;
            $itemDescription = "";
            $isThereAnyValidItems = false;
            foreach ($itemCollection as $item) {
                list($length, $width, $height, $weight) = $this->getDimensionsFromItem($item);
                if ($item->getQty() > 0) {
                    $finalWeight += $weight;
                    $itemDescription .= $item->getSku();
                    $isThereAnyValidItems = true;
                }
            }

            /*
            causes Client error response - 400 - Bad Request
            */
            if (strlen($itemDescription) > self::MAX_ITEM_DESCRIPTION_LEN) {
                $this->hlp()->log(
                    $this->hlp()->__(
                        "%->truncate[%d]: %s",
                        __METHOD__,
                        self::MAX_ITEM_DESCRIPTION_LEN, $itemDescription
                    )
                );
                $itemDescription = substr($itemDescription, 0, self::MAX_ITEM_DESCRIPTION_LEN);
            }

            /*
            any non-alphanumeric chars cause a 400 - Bad Request
            */
            if (preg_match("/[^A-Za-z0-9 ]/", $itemDescription)) {
                $this->hlp()->log(
                    $this->hlp()->__(
                        "%->strip non-alphanumeric chars from: '%s'",
                        __METHOD__,
                        $itemDescription
                    )
                );
                $itemDescription = preg_replace("/[^A-Za-z0-9 ]/", '', $itemDescription);
            }

            if ($isThereAnyValidItems) {
                $itemsData[] = array(
                    'item_reference' => Mage::helper('core')->uniqHash(),
                    'item_description' => $itemDescription,
                    'product_id' => $productId,
                    'length' => $length,
                    'width' => $width,
                    'height' => $height,
                    'weight' => $finalWeight,
                    'authority_to_leave' => true,
                );
            }
        }

        return $itemsData;
    }

}