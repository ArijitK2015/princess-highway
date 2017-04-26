<?php

/**
 * Class FactoryX_ShippedFrom_Model_Auspost_Shipping_Labels
 */
class FactoryX_ShippedFrom_Model_Auspost_Shipping_Labels extends FactoryX_ShippedFrom_Model_Auspost_Shipping_Abstract
{
    /**
     * @param FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry
     * @param null $cronLogger
     */
    public function getAuspostLabel(FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry, $cronLogger = null)
    {
        $accountNo = $this->getAccountNumberFromShipment($queueEntry->getShippedFrom());
        $apiKey = $this->getApiKeyFromShipment($queueEntry->getShippedFrom());
        $apiPassword = $this->getApiPasswordFromShipment($queueEntry->getShippedFrom());

        /** @var Auspost\Shipping\ShippingClient $shipTrack */
        $shipTrack = $this->initShipTrack($apiKey, $apiPassword, $accountNo);
        $requestId = $queueEntry->getApRequestId();

        $labelRetrievalSummary = array();

        $cronMessage = $this->hlp()->__('Start GetLabel for shipment with request %s', $requestId);
        $this->addCronLoggerMessage($cronLogger, $cronMessage);

        try {
            $labelRetrievalSummary = $shipTrack->GetLabel(array('request_id' =>  $requestId));
            $cronMessage = $this->hlp()->__('End GetLabel for shipment with request %s', $requestId);
            $this->addCronLoggerMessage($cronLogger, $cronMessage);
        } catch(Guzzle\Http\Exception\BadResponseException $e) {
            $message = $e->getMessage();
            $cronMessage = $message;
            $this->addCronLoggerMessage($cronLogger, $cronMessage);
        }

        if (!$labelRetrievalSummary && $message) {
            $this->handleErrors(
                $queueEntry,
                array(
                    'errors'    =>  array(
                        'message' =>  $message
                    )
                )
            );
        } else if (array_key_exists('errors', $labelRetrievalSummary)) {
            $this->handleErrors($queueEntry, $labelRetrievalSummary);
        } else {
            $labelUri = $labelRetrievalSummary['labels'][0]['url'];

            $cronMessage = $this->hlp()->__('Start Downloading label with URI: %s', $labelUri);
            $this->addCronLoggerMessage($cronLogger, $cronMessage);

            $fileDir = Mage::getBaseDir('media') . DS . 'auspost';
            $fileName = md5($labelUri) . '.pdf';
            $filepath = $fileDir . DS . $fileName;

            if (!is_dir($fileDir)) {
                $io = new Varien_Io_File();
                $io->checkAndCreateFolder($fileDir);
            }

            file_put_contents($filepath, file_get_contents($labelUri));

            $cronMessage = $this->hlp()->__('End Downloading label with URI: %s', $labelUri);
            $this->addCronLoggerMessage($cronLogger, $cronMessage);

            $cronMessage = $this->hlp()->__('Start Saving shipping queue %s', $queueEntry->getEntityId());
            $this->addCronLoggerMessage($cronLogger, $cronMessage);

            $queueEntry->setData('status', FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_LABEL_CREATED)
                ->setData('ap_label_uri', $labelUri)
                ->setData('local_label_link', Mage::getBaseUrl('media') . DS . 'auspost' . DS . $fileName)
                ->setData('ap_last_message', '')
                ->setData('ap_last_url', '')
                ->save();

            $cronMessage = $this->hlp()->__('End Saving shipping queue %s', $queueEntry->getEntityId());
            $this->addCronLoggerMessage($cronLogger, $cronMessage);
        }
    }

    /**
     * @param FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry
     * @param null $cronLogger
     */
    public function initAuspostLabel(FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry, $cronLogger = null)
    {
        $shippedFrom = $queueEntry->getShippedFrom();

        $accountNo = $this->getAccountNumberFromShipment($shippedFrom);
        $apiKey = $this->getApiKeyFromShipment($shippedFrom);
        $apiPassword = $this->getApiPasswordFromShipment($shippedFrom);

        /** @var Auspost\Shipping\ShippingClient $shipTrack */
        $shipTrack = $this->initShipTrack($apiKey, $apiPassword, $accountNo);
        $apShipmentId = $queueEntry->getApShipmentId();

        /** @var Mage_Sales_Model_Order_Shipment $shipment */
        $shipment = $this->hlpAuspost()->getShipmentFromQueueEntry($queueEntry);

        $labelData = $this->generateAuspostLabelData($apShipmentId, $shipment, $shippedFrom);

        $json = array();
        $json['body'] = Mage::helper('core')->jsonEncode($labelData);

        $this->saveJsonRequest($queueEntry, $labelData);

        $labelCreationSummary = array();

        $cronMessage = $this->hlp()->__('Start CreateMabem for shipment %s', $apShipmentId);
        $this->addCronLoggerMessage($cronLogger, $cronMessage);
        try {
            $labelCreationSummary = $shipTrack->CreateLabels($json);
            $cronMessage = $this->hlp()->__('End CreateMabem for shipment %s', $apShipmentId);
            $this->addCronLoggerMessage($cronLogger, $cronMessage);
        } catch(Guzzle\Http\Exception\BadResponseException $e) {
            $message = $e->getMessage();
            $cronMessage = $message;
            $this->addCronLoggerMessage($cronLogger, $cronMessage);
        }

        if (!$labelCreationSummary && $message) {
            $this->handleErrors(
                $queueEntry,
                array(
                    'errors'    =>  array(
                        'message' =>  $message
                    )
                )
            );
        } else if (array_key_exists('errors', $labelCreationSummary)) {
            $this->handleErrors($queueEntry, $labelCreationSummary);
        } else {
            $queueEntry->setData('status', FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_LABEL_INITIALIZED)
                ->setData('ap_request_id', $labelCreationSummary['labels'][0]['request_id'])
                ->setData('ap_last_message', '')
                ->setData('ap_last_url', '')
                ->save();
        }
    }

    /**
     * @param string $shipmentId
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @param $shippedFrom
     * @return array
     */
    protected function generateAuspostLabelData($shipmentId, Mage_Sales_Model_Order_Shipment $shipment, $shippedFrom)
    {
        try {
            $data = array();
            $data['preferences'] = array();
            $groupsDetails = array();

            $group = $this->getLabelGroupFromShipmentId($shipment);

            list(
                $labelLayout,
                $labelBranded,
                $labelLeftOffset,
                $labelTopOffset
                ) = $this->getLabelConfiguration($shippedFrom);

            if ($group) {
                $groupsDetails[] = array(
                    'group'         =>  "Parcel Post",
                    'layout'        =>  $labelLayout,
                    'branded'       =>  $labelBranded,
                    'left_offset'   =>  $labelLeftOffset,
                    'top_offset'    =>  $labelTopOffset
                );
                $data['preferences'][] = array(
                    'type'      =>   'PRINT',
                    'groups'    =>  $groupsDetails
                );
                $data['shipments'][] = array(
                    'shipment_id'    =>  $shipmentId
                );
                return $data;
            } else {
                Mage::throwException($this->hlp()->__('Invalid label group'));
            }
        } catch (Exception $e) {
            $this->hlp()->log($e->getMessage());
        }

        return $data;
    }

    /**
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @return bool|string
     */
    protected function getLabelGroupFromShipmentId(Mage_Sales_Model_Order_Shipment $shipment)
    {
        $shippingMethod = $shipment->getOrder()->getShippingMethod(true);
        $this->hlp()->log(sprintf("%s->method=%s", __METHOD__, $shippingMethod['method']));

        $defaultEbayProduct = $this->hlpAuspost()->getDefaultEbayProduct();
        $this->hlp()->log(sprintf("%s->defaultEbayProduct=%s", __METHOD__, print_r($defaultEbayProduct, true)));

        if (preg_match('/express/i', $shippingMethod['method'])) {
            $group = "Express Post";
        } elseif (preg_match('/parcel/i', $shippingMethod['method'])) {
            $group = "Parcel Post";
        } elseif ($shippingMethod['method'] == "freeshipping") {
            $defaultFreeShipping = $this->hlpAuspost()->getDefaultFreeShippingProduct();
            if (preg_match('/express/i', $defaultFreeShipping)) {
                $group = "Express Post";
            } elseif (preg_match('/parcel/i', $defaultFreeShipping)) {
                $group = "Parcel Post";
            } else {
                $group = false;
            }
        } elseif ($shippingMethod['method'] == "m2eproshipping") {
            $defaultEbayProduct = $this->hlpAuspost()->getDefaultEbayProduct();
            foreach ($defaultEbayProduct as $ebayMethod => $ausPostGroup) {
                if (preg_match('/express/i', $ausPostGroup)) {
                    $group = "Express Post";
                    break;
                } elseif (preg_match('/parcel/i', $ausPostGroup)) {
                    $group = "Parcel Post";
                    break;
                } else {
                    $group = false;
                }
            }
        } else {
            $group = false;
        }

        return $group;
    }

    /**
     * @param string $shippedFrom
     * @return array
     */
    protected function getLabelConfiguration($shippedFrom)
    {
        $collection = Mage::getResourceModel('ustorelocator/location_collection')
            ->addFieldToFilter('location_id', $shippedFrom)
            ->addFieldToSelect(array('label_layout', 'label_branded', 'label_left_offset', 'label_top_offset'))
            ->setPageSize(1)
            ->setCurPage(1);

        if ($collection->getSize()) {
            $location = $collection->getFirstItem();
            $labelLayout = $location->getLabelLayout()
                ? $location->getLabelLayout()
                : $this->hlpAuspost()->getLayout();
            $labelBranded = $location->getLabelBranded()
                ? $location->getLabelBranded()
                : $this->hlpAuspost()->isBranded();
            $labelLeftOffset = $location->getLabelLeftOffset()
                ? $location->getLabelLeftOffset()
                : $this->hlpAuspost()->getLeftOffset();
            $labelTopOffset = $location->getLabelTopOffset()
                ? $location->getLabelTopOffset()
                : $this->hlpAuspost()->getTopOffset();
            return array($labelLayout, $labelBranded, $labelLeftOffset, $labelTopOffset);
        } else {
            $labelLayout = $this->hlpAuspost()->getLayout();
            $labelBranded = $this->hlpAuspost()->isBranded();
            $labelLeftOffset = $this->hlpAuspost()->getLeftOffset();
            $labelTopOffset = $this->hlpAuspost()->getTopOffset();
            return array($labelLayout, $labelBranded, $labelLeftOffset, $labelTopOffset);
        }
    }
}