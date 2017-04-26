<?php

/**
 * Class FactoryX_ShippedFrom_Model_Auspost_Shipping_Orders
 */
class FactoryX_ShippedFrom_Model_Auspost_Shipping_Orders extends FactoryX_ShippedFrom_Model_Auspost_Shipping_Abstract
{
    /**
     * @param array $groupedShipments
     * @param FactoryX_ShippedFrom_Model_Cron_Log|null $cronLogger
     */
    public function createAuspostOrder(array $groupedShipments, $cronLogger = null)
    {
        foreach ($groupedShipments as $shippedFrom => $shipment) {
            $accountNo = $this->getAccountNumberFromShipment($shippedFrom);
            $apiKey = $this->getApiKeyFromShipment($shippedFrom);
            $apiPassword = $this->getApiPasswordFromShipment($shippedFrom);

            /** @var Auspost\Shipping\ShippingClient $shipTrack */
            $shipTrack = $this->initShipTrack($apiKey, $apiPassword, $accountNo);
            $orderData = $this->generateOrderData($shipment);

            $json = array();
            $json['body'] = Mage::helper('core')->jsonEncode($orderData);

            $orderCreationSummary = array();

            $cronMessage = $this->hlp()->__('Start CreateOrderFromShipments for shipment %s', print_r($shipment, true));
            $this->addCronLoggerMessage($cronLogger, $cronMessage);

            try {
                $orderCreationSummary = $shipTrack->CreateOrderFromShipments($json);
                $cronMessage = $this->hlp()->__(
                    'End CreateOrderFromShipments for shipment %s',
                    print_r($shipment, true)
                );
                $this->addCronLoggerMessage($cronLogger, $cronMessage);
            } catch(Guzzle\Http\Exception\BadResponseException $e) {
                $message = $e->getMessage();
                $cronMessage = $message;
                $this->addCronLoggerMessage($cronLogger, $cronMessage);
            }

            $apShipmentIds = array();

            foreach ($shipment as $ship) {
                $apShipmentIds[] = $ship['shipment_id'];
            }

            /** @var FactoryX_ShippedFrom_Model_Resource_Shipping_Queue_Collection $queueCollection */
            $queueCollection = Mage::getResourceModel('shippedfrom/shipping_queue_collection')
                ->addFieldToFilter('shipped_from', $shippedFrom)
                ->addFieldToFilter('ap_shipment_id', array('in' => $apShipmentIds))
                ->addFieldToFilter('status', FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_LABEL_SENT);

            $this->massSaveJsonRequest($queueCollection, $orderData);

            if (!$orderCreationSummary && $message) {
                $this->massHandleErrors(
                    $queueCollection,
                    array(
                        'errors'    =>  array(
                            'message' =>  $message
                        )
                    )
                );
            } else if (array_key_exists('errors', $orderCreationSummary)) {
                $this->massHandleErrors($queueCollection, $orderCreationSummary);
            } else {
                $apOrderId = $orderCreationSummary['order']['order_id'];
                $merchantLocationId = $this->getMerchantLocationIdFromShipment($shippedFrom);

                $cronMessage = $this->hlp()->__('Start Create Magento Auspost Order %s', $apOrderId);
                $this->addCronLoggerMessage($cronLogger, $cronMessage);

                /** @var FactoryX_ShippedFrom_Model_Orders $order */
                $order = Mage::getModel('shippedfrom/orders');
                $order->setData('ap_order_id', $apOrderId);
                $order->setData('order_reference', $orderCreationSummary['order']['order_reference']);
                $order->setData('status', $orderCreationSummary['order']['order_summary']['status']);
                $order->setData('merchant_location_id', $merchantLocationId);
                $order->setData('created_at', strftime('%Y-%m-%d %H:%M:00', time()) );
                $order->save();

                $cronMessage = $this->hlp()->__('End Create Magento Auspost Order %s', $apOrderId);
                $this->addCronLoggerMessage($cronLogger, $cronMessage);

                if ($this->hlpAuspost()->chargeToMaster()) {
                    /** @var FactoryX_ShippedFrom_Model_Auspost_Shipping_Payments $auspostPayment */
                    $auspostPayment = Mage::getModel('shippedfrom/auspost_shipping_payments');
                    $auspostPayment->pay($apOrderId, $this->hlpAuspost()->getGlobalAccountNo());
                }

                $cronMessage = $this->hlp()->__('Start Changing queue collection order status');
                $this->addCronLoggerMessage($cronLogger, $cronMessage);

                $queueCollection->massUpdate(
                    array(
                        'status'            =>  FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_ORDERED,
                        'ap_order_id'       => $apOrderId,
                        'ap_last_message'   => ''
                    )
                );

                $cronMessage = $this->hlp()->__('End Changing queue collection order status');
                $this->addCronLoggerMessage($cronLogger, $cronMessage);
            }
        }
    }

    /**
     * @param $shipment
     * @return array
     */
    protected function generateOrderData($shipment)
    {
        $orderData = array();
        $orderData['order_reference'] = Mage::helper('core')->uniqHash();
        $orderData['payment_method'] = "CHARGE_TO_ACCOUNT";
        $orderData['shipments'] = $shipment;
        return $orderData;
    }

    /**
     * @param $orderId
     * @return string
     */
    public function getSummary($orderId)
    {
        $this->hlp()->log(sprintf("%s->orderId=%s", __METHOD__, $orderId));
        $queueCollection = Mage::getResourceModel('shippedfrom/shipping_queue_collection')
            ->addFieldToSelect('shipped_from')
            ->addFieldToFilter('ap_order_id', $orderId)
            ->setPageSize(1)
            ->setCurPage(1);

        if (!$queueCollection->getSize()) {
            Mage::getSingleton('core/session')->addError(
                $this->hlp()->__("Could not retrieve order %s", $orderId)
            );
            return "";
        }

        $shippedFrom = $queueCollection->getFirstItem()->getShippedFrom();

        $accountNo = $this->getAccountNumberFromShipment($shippedFrom);
        $apiKey = $this->getApiKeyFromShipment($shippedFrom);
        $apiPassword = $this->getApiPasswordFromShipment($shippedFrom);

        $fileDir = Mage::getBaseDir('media') . DS . 'auspost';
        $fileName = md5($orderId) . '.pdf';
        $filepath = $fileDir . DS . $fileName;
        $this->hlp()->log(sprintf("%s->filepath=%s", __METHOD__, $filepath));
        
        if (!is_dir($fileDir)) {
            $io = new Varien_Io_File();
            $io->checkAndCreateFolder($fileDir);
        }

        /** @var Auspost\Shipping\ShippingClient $shipTrack */
        $shipTrack = $this->initShipTrack($apiKey, $apiPassword, $accountNo, $filepath);

        $orderSummary = array();
        try {
            $orderSummary = $shipTrack->GetOrderSummary(
                array(
                    'account_number' => $accountNo,
                    'order_id' => $orderId
                )
            );
            $this->hlp()->log(sprintf("%s->orderSummary=%s", __METHOD__, print_r($orderSummary, true)));
        }
        catch(Guzzle\Http\Exception\BadResponseException $e) {
            $message = $e->getMessage();
        }

        if (!$orderSummary && $message) {
            $this->massHandleErrors(
                $queueCollection,
                array(
                    'errors'    =>  array(
                        'message' =>  $message
                    )
                )
            );
        } else if (array_key_exists('errors', $orderSummary)) {
            $this->massHandleErrors($queueCollection, $orderSummary);
        } else {
            if (copy($orderSummary[0], $filepath)) {
                $this->hlp()->log(sprintf("%s->copyTo: %s", __METHOD__, $filepath));
            } else {
                Mage::throwException($this->hlp()->__("cannot create file '%s'", $filepath));
            }

            if (unlink($orderSummary[0])) {
                $this->hlp()->log(sprintf("%s->delete %s", __METHOD__, $orderSummary[0]));
            } else {
                Mage::throwException($this->hlp()->__("cannot remove file '%s'", $orderSummary[0]));
            }

            $orderCollection = Mage::getResourceModel('shippedfrom/orders_collection')
                ->addFieldToFilter('ap_order_id', $orderId)
                ->setPageSize(1)
                ->setCurPage(1);

            $this->hlp()->log(sprintf("%s->filepath: %s", __METHOD__, $filepath));
            if ($orderCollection->getSize()) {
                $order = $orderCollection->getFirstItem();
                $order->setOrderSummaryLink($filepath);
                $order->save();
            }
        }

        return $filepath;
    }

    /**
     * @param $orderId
     *
     * update status
     * [order]
     *     [order_creation_date] => 2017-03-30T22:15:30+11:00
     *     [order_summary] => Array
     *         [total_cost] => 151.49
     *         [status] => Initiated
     *
     *
     * @return string
     */
    public function updateOrder($orderId)
    {
        $orderCollection = Mage::getResourceModel('shippedfrom/orders_collection')
            ->addFieldToSelect(array('ap_order_id','merchant_location_id'))
            ->addFieldToFilter('order_id', $orderId)
            ->setPageSize(1)
            ->setCurPage(1);
        if (!$orderCollection->getSize()) {
            Mage::getSingleton('core/session')->addError($this->hlp()->__("Could not retrieve order %s", $orderId));
            return "";
        }
        $order = $orderCollection->getFirstItem();
        $mlid = $order->getData('merchant_location_id');

        $accountNo = $this->getAccountInfo($mlid, 'account_no');        
        $apiKey = $this->getAccountInfo($mlid, 'api_key');
        $apiPassword = $this->getAccountInfo($mlid, 'api_pwd');

        /** @var Auspost\Shipping\ShippingClient $shipTrack */
        $shipTrack = $this->initShipTrack($apiKey, $apiPassword, $accountNo, $filepath);

        $orderDetails = array();
        try {
            $orderDetails = $shipTrack->GetOrder(
                array(
                    'order_id' => $order->getData('ap_order_id')
                )
            );
        }
        catch(Guzzle\Http\Exception\BadResponseException $e) {
            $message = $e->getMessage();
        }

        if (!$orderDetails && $message) {
            Mage::getSingleton('core/session')->addError($this->hlp()->__($message));
            return "";
        }
        else if (array_key_exists('errors', $orderDetails)) {
            Mage::getSingleton('core/session')->addError($this->hlp()->__($orderDetails['errors']));
            return "";
        }
        else {
            $order = $orderCollection->getFirstItem();
            // status
            $order->setStatus($orderDetails['order']['order_summary']['status']);
            // total_cost
            if (
                $orderDetails['order']['order_summary']['total_cost']
                && 
                is_float($orderDetails['order']['order_summary']['total_cost'])
            ) {
                $this->hlp()->log(sprintf("%s->orderDetails['order']['order_summary']['total_cost']=%s", __METHOD__, $orderDetails['order']['order_summary']['total_cost']));    
                $order->setData('total_cost', $orderDetails['order']['order_summary']['total_cost']);
            }
            // number_of_shipments
            if (
                $orderDetails['order']['shipments']
                && 
                is_array($orderDetails['order']['shipments'])
            ) {
                $this->hlp()->log(sprintf("%s->orderDetails['order']['order_summary']['shipments']=%d", __METHOD__, count($orderDetails['order']['shipments'])) );    
                $order->setData('number_of_shipments', count($orderDetails['order']['shipments']));
            }            
            // created_at
            if (empty($order->getData('created_at'))) {
                // note. AusPost returns date with a timezone, this should be converted to UTC first
                $order->setCreatedAt($orderDetails['order']['order_creation_date']);
            }
            $order->save();
        }
        return $this;
    }

    /**
     * key = account_no | api_key | api_pwd
     */
    public function getAccountInfo($mlid, $key = 'account_no') {
        $collection = Mage::getResourceModel('shippedfrom/account_collection')
            ->addFieldToFilter('merchant_location_id', $mlid)
            ->addFieldToSelect($key)
            ->setPageSize(1)
            ->setCurPage(1);
        if ($mlid && $collection->getSize()) {
            $account = $collection->getFirstItem();
            return $account->getData($key);
        }
        return $this->hlpAuspost()->getGlobalInfo($key);
    }
}