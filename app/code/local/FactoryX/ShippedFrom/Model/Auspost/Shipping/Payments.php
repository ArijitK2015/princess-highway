<?php

/**
 * Class FactoryX_ShippedFrom_Model_Auspost_Shipping_Payments
 */
class FactoryX_ShippedFrom_Model_Auspost_Shipping_Payments
    extends FactoryX_ShippedFrom_Model_Auspost_Shipping_Abstract
{
    /**
     * @param $orderId
     * @param null $accountToCharge
     */
    public function pay($orderId, $accountToCharge = null)
    {
        $queueCollection = Mage::getResourceModel('shippedfrom/shipping_queue_collection')
            ->addFieldToSelect('shipped_from')
            ->addFieldToFilter('ap_order_id', $orderId)
            ->setPageSize(1)
            ->setCurPage(1);

        if (!$queueCollection->getSize()) {
            return;
        }

        $shippedFrom = $queueCollection->getFirstItem()->getShippedFrom();

        $accountNo = $this->getAccountNumberFromShipment($shippedFrom);
        $apiKey = $this->getApiKeyFromShipment($shippedFrom);
        $apiPassword = $this->getApiPasswordFromShipment($shippedFrom);

        /** @var Auspost\Shipping\ShippingClient $shipTrack */
        $shipTrack = $this->initShipTrack($apiKey, $apiPassword, $accountNo);

        if ($accountToCharge === null) {
            $accountToCharge = $accountNo;
        }

        $paymentSummary = array();

        try {
            $paymentSummary = $shipTrack->CreatePayment(
                array(
                    'order_id'          =>  $orderId,
                    'payment_method'    =>  "CHARGE_TO_ACCOUNT",
                    'payment_reference' =>  "Payment for order #$orderId",
                    'attributes'        =>  array(
                        'account_number'  =>  $accountToCharge
                    )
                )
            );
        } catch(Guzzle\Http\Exception\BadResponseException $e) {
            $message = $e->getMessage();
        }

        if (!$paymentSummary && $message) {
            $this->massHandleErrors(
                $queueCollection,
                array(
                    'errors'    =>  array(
                        'message' =>  $message
                    )
                )
            );
        } else if (array_key_exists('errors', $paymentSummary)) {
            $this->massHandleErrors($queueCollection, $paymentSummary);
        } else {
            $paymentId = $paymentSummary['payment_id'];
            $order = Mage::getModel('shippedfrom/orders')->load($orderId, 'ap_order_id');
            $order->setApPaymentId($paymentId);
            $order->setChargeAccount($accountToCharge);
            $order->save();
        }
    }

}